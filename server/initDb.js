// Creates the schema and seeds it. Safe to re-run: tables use IF NOT EXISTS,
// and seed rows use ON CONFLICT DO NOTHING so existing data / admin changes
// are preserved.
import { pool, query } from "./db.js"
import { ensureSchema } from "./schema.js"
import { SEED_JOBS } from "../src/data/jobs.js"
import { DEMO_USERS } from "../src/data/users.js"

async function main() {
  console.log("Connecting to database…")
  await query("SELECT 1")
  console.log("Connected. Creating tables…")

  await ensureSchema(query)

  console.log("Seeding demo users…")
  for (const u of DEMO_USERS) {
    await query(
      `INSERT INTO users (id, name, email, password, role)
       VALUES ($1,$2,$3,$4,$5)
       ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name, email = EXCLUDED.email,
         password = EXCLUDED.password, role = EXCLUDED.role`,
      [u.id, u.name, u.email, u.password, u.role]
    )
  }

  console.log("Seeding jobs…")
  for (const j of SEED_JOBS) {
    await query(
      `INSERT INTO jobs
        (id, title, org, posted_by, shared_note, category, type, location, pay,
         posted_at, description, requirements, contact_email, tags, status)
       VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15)
       ON CONFLICT (id) DO NOTHING`,
      [
        j.id,
        j.title,
        j.org,
        j.postedBy,
        j.sharedNote ?? null,
        j.category,
        j.type,
        j.location,
        j.pay ?? null,
        j.postedAt,
        j.description,
        JSON.stringify(j.requirements ?? []),
        j.contactEmail,
        JSON.stringify(j.tags ?? []),
        j.status ?? "approved",
      ]
    )
  }

  const { rows } = await query("SELECT status, count(*) FROM jobs GROUP BY status ORDER BY status")
  console.log("Jobs by status:", rows)
  const users = await query("SELECT count(*) FROM users")
  console.log("Users:", users.rows[0].count)

  console.log("Done.")
  await pool.end()
}

main().catch((err) => {
  console.error("init-db failed:", err)
  process.exit(1)
})
