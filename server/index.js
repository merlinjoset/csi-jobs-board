import express from "express"
import cors from "cors"
import dotenv from "dotenv"
import path from "path"
import { fileURLToPath } from "url"
import { query } from "./db.js"
import { ensureSchema } from "./schema.js"

dotenv.config()

const __dirname = path.dirname(fileURLToPath(import.meta.url))

const app = express()
app.use(cors())
app.use(express.json())

// --- helpers ---------------------------------------------------------------

// Map a DB row (snake_case) to the shape the frontend already expects (camelCase).
function rowToJob(r) {
  return {
    id: r.id,
    title: r.title,
    org: r.org,
    postedBy: r.posted_by,
    sharedNote: r.shared_note ?? undefined,
    category: r.category,
    type: r.type,
    location: r.location,
    pay: r.pay,
    postedAt:
      r.posted_at instanceof Date ? r.posted_at.toISOString().slice(0, 10) : r.posted_at,
    description: r.description,
    requirements: r.requirements ?? [],
    contactEmail: r.contact_email,
    tags: r.tags ?? [],
    status: r.status,
  }
}

// Lightweight prototype auth: the client sends the signed-in user's id in a
// header. We look the user up to get their role. (A real app would use signed
// sessions / JWTs and hashed passwords.)
async function getUser(req) {
  const id = req.header("x-user-id")
  if (!id) return null
  const { rows } = await query("SELECT id, name, email, role FROM users WHERE id = $1", [id])
  return rows[0] ?? null
}

function asyncH(fn) {
  return (req, res) => fn(req, res).catch((err) => {
    console.error(err)
    res.status(500).json({ error: "Server error" })
  })
}

// --- routes ----------------------------------------------------------------

app.get("/api/health", asyncH(async (_req, res) => {
  await query("SELECT 1")
  res.json({ ok: true })
}))

app.post("/api/login", asyncH(async (req, res) => {
  const { email, password } = req.body ?? {}
  const { rows } = await query(
    "SELECT id, name, email, role FROM users WHERE lower(email) = lower($1) AND password = $2",
    [String(email ?? "").trim(), String(password ?? "")]
  )
  // 200 with ok:false so the client can show the message without treating it
  // as a transport error.
  if (!rows[0]) return res.json({ ok: false, error: "Incorrect email or password." })
  res.json({ ok: true, user: rows[0] })
}))

// Public board: approved postings only.
app.get("/api/jobs", asyncH(async (_req, res) => {
  const { rows } = await query(
    "SELECT * FROM jobs WHERE status = 'approved' ORDER BY posted_at DESC, created_at DESC"
  )
  res.json(rows.map(rowToJob))
}))

// Admin: every posting, any status.
app.get("/api/admin/jobs", asyncH(async (req, res) => {
  const user = await getUser(req)
  if (!user || user.role !== "admin") return res.status(403).json({ error: "Admins only" })
  const { rows } = await query("SELECT * FROM jobs ORDER BY posted_at DESC, created_at DESC")
  res.json(rows.map(rowToJob))
}))

// Create a posting (signed-in members). Arrives as pending for review.
app.post("/api/jobs", asyncH(async (req, res) => {
  const user = await getUser(req)
  if (!user) return res.status(401).json({ error: "Please sign in to share an opportunity." })
  const j = req.body ?? {}
  const id = `user-${Date.now()}`
  const { rows } = await query(
    `INSERT INTO jobs
      (id, title, org, posted_by, shared_note, category, type, location, pay,
       posted_at, description, requirements, contact_email, tags, status)
     VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9, CURRENT_DATE, $10,$11,$12,$13,'pending')
     RETURNING *`,
    [
      id,
      j.title,
      j.org,
      j.postedBy,
      j.sharedNote || null,
      j.category,
      j.type,
      j.location,
      j.pay || "Not specified",
      j.description,
      JSON.stringify(j.requirements ?? []),
      j.contactEmail,
      JSON.stringify(j.tags ?? []),
    ]
  )
  res.status(201).json(rowToJob(rows[0]))
}))

// Update status (admin): approved | rejected | closed | pending.
app.patch("/api/jobs/:id/status", asyncH(async (req, res) => {
  const user = await getUser(req)
  if (!user || user.role !== "admin") return res.status(403).json({ error: "Admins only" })
  const { status } = req.body ?? {}
  const allowed = ["approved", "rejected", "closed", "pending"]
  if (!allowed.includes(status)) return res.status(400).json({ error: "Invalid status" })
  const { rows } = await query(
    "UPDATE jobs SET status = $1 WHERE id = $2 RETURNING *",
    [status, req.params.id]
  )
  if (!rows[0]) return res.status(404).json({ error: "Not found" })
  res.json(rowToJob(rows[0]))
}))

// Hard delete (admin).
app.delete("/api/jobs/:id", asyncH(async (req, res) => {
  const user = await getUser(req)
  if (!user || user.role !== "admin") return res.status(403).json({ error: "Admins only" })
  await query("DELETE FROM jobs WHERE id = $1", [req.params.id])
  res.json({ ok: true })
}))

// Shortlist (signed-in members).
app.get("/api/shortlist", asyncH(async (req, res) => {
  const user = await getUser(req)
  if (!user) return res.status(401).json({ error: "Sign in required" })
  const { rows } = await query("SELECT job_id FROM shortlists WHERE user_id = $1", [user.id])
  res.json(rows.map((r) => r.job_id))
}))

app.post("/api/shortlist/:jobId/toggle", asyncH(async (req, res) => {
  const user = await getUser(req)
  if (!user) return res.status(401).json({ error: "Sign in required" })
  const jobId = req.params.jobId
  const existing = await query(
    "SELECT 1 FROM shortlists WHERE user_id = $1 AND job_id = $2",
    [user.id, jobId]
  )
  if (existing.rows.length) {
    await query("DELETE FROM shortlists WHERE user_id = $1 AND job_id = $2", [user.id, jobId])
  } else {
    await query(
      "INSERT INTO shortlists (user_id, job_id) VALUES ($1, $2) ON CONFLICT DO NOTHING",
      [user.id, jobId]
    )
  }
  const { rows } = await query("SELECT job_id FROM shortlists WHERE user_id = $1", [user.id])
  res.json(rows.map((r) => r.job_id))
}))

// --- admin: user management ------------------------------------------------

function requireAdmin(user, res) {
  if (!user || user.role !== "admin") {
    res.status(403).json({ error: "Admins only" })
    return false
  }
  return true
}

// List all users with a couple of derived activity counts.
app.get("/api/admin/users", asyncH(async (req, res) => {
  const admin = await getUser(req)
  if (!requireAdmin(admin, res)) return
  const { rows } = await query(`
    SELECT u.id, u.name, u.email, u.role,
      (SELECT count(*) FROM jobs j WHERE j.posted_by = u.name) AS jobs_posted,
      (SELECT count(*) FROM shortlists s WHERE s.user_id = u.id) AS shortlist_count
    FROM users u
    ORDER BY (u.role = 'admin') DESC, u.name
  `)
  res.json(
    rows.map((r) => ({
      id: r.id,
      name: r.name,
      email: r.email,
      role: r.role,
      jobsPosted: Number(r.jobs_posted),
      shortlistCount: Number(r.shortlist_count),
    }))
  )
}))

// Create a user.
app.post("/api/admin/users", asyncH(async (req, res) => {
  const admin = await getUser(req)
  if (!requireAdmin(admin, res)) return
  const { name, email, password, role } = req.body ?? {}
  if (!name?.trim() || !email?.trim() || !password) {
    return res.status(400).json({ error: "Name, email and password are required." })
  }
  if (!["admin", "member"].includes(role)) return res.status(400).json({ error: "Invalid role" })
  const exists = await query("SELECT 1 FROM users WHERE lower(email) = lower($1)", [email.trim()])
  if (exists.rows.length) {
    return res.status(409).json({ error: "A user with that email already exists." })
  }
  const id = `user-${Date.now()}`
  const { rows } = await query(
    "INSERT INTO users (id, name, email, password, role) VALUES ($1,$2,$3,$4,$5) RETURNING id, name, email, role",
    [id, name.trim(), email.trim(), password, role]
  )
  res.status(201).json(rows[0])
}))

// Change a user's role, protecting against removing the last admin.
app.patch("/api/admin/users/:id/role", asyncH(async (req, res) => {
  const admin = await getUser(req)
  if (!requireAdmin(admin, res)) return
  const { role } = req.body ?? {}
  if (!["admin", "member"].includes(role)) return res.status(400).json({ error: "Invalid role" })
  const target = await query("SELECT role FROM users WHERE id = $1", [req.params.id])
  if (!target.rows[0]) return res.status(404).json({ error: "Not found" })
  if (role === "member" && target.rows[0].role === "admin") {
    const admins = await query("SELECT count(*) FROM users WHERE role = 'admin'")
    if (Number(admins.rows[0].count) <= 1) {
      return res.status(400).json({ error: "Cannot demote the last admin." })
    }
  }
  const { rows } = await query(
    "UPDATE users SET role = $1 WHERE id = $2 RETURNING id, name, email, role",
    [role, req.params.id]
  )
  res.json(rows[0])
}))

// Delete a user (cannot delete yourself or the last admin).
app.delete("/api/admin/users/:id", asyncH(async (req, res) => {
  const admin = await getUser(req)
  if (!requireAdmin(admin, res)) return
  if (admin.id === req.params.id) {
    return res.status(400).json({ error: "You cannot delete your own account." })
  }
  const target = await query("SELECT role FROM users WHERE id = $1", [req.params.id])
  if (!target.rows[0]) return res.status(404).json({ error: "Not found" })
  if (target.rows[0].role === "admin") {
    const admins = await query("SELECT count(*) FROM users WHERE role = 'admin'")
    if (Number(admins.rows[0].count) <= 1) {
      return res.status(400).json({ error: "Cannot delete the last admin." })
    }
  }
  await query("DELETE FROM users WHERE id = $1", [req.params.id])
  res.json({ ok: true })
}))

// In production, serve the built React app from the same service so /api is
// same-origin (no CORS or proxy needed).
if (process.env.NODE_ENV === "production") {
  const dist = path.join(__dirname, "..", "dist")
  app.use(express.static(dist))
  app.get("*", (req, res) => {
    if (req.path.startsWith("/api")) return res.status(404).json({ error: "Not found" })
    res.sendFile(path.join(dist, "index.html"))
  })
}

const PORT = process.env.PORT || 3001

// Make sure the tables exist before accepting traffic, then start listening.
ensureSchema(query)
  .then(() => {
    app.listen(PORT, () => console.log(`Server listening on http://localhost:${PORT}`))
  })
  .catch((err) => {
    console.error("Failed to ensure schema:", err)
    process.exit(1)
  })
