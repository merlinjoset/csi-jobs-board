// Shared schema definition. Used by initDb.js (create + seed) and by the
// server on startup (create only), so a fresh deployment self-heals its tables.
export async function ensureSchema(query) {
  await query(`
    CREATE TABLE IF NOT EXISTS users (
      id       TEXT PRIMARY KEY,
      name     TEXT NOT NULL,
      email    TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      role     TEXT NOT NULL DEFAULT 'member'
    );
  `)

  await query(`
    CREATE TABLE IF NOT EXISTS jobs (
      id            TEXT PRIMARY KEY,
      title         TEXT NOT NULL,
      org           TEXT NOT NULL,
      posted_by     TEXT NOT NULL,
      shared_note   TEXT,
      category      TEXT NOT NULL,
      type          TEXT NOT NULL,
      location      TEXT NOT NULL,
      pay           TEXT,
      posted_at     DATE NOT NULL DEFAULT CURRENT_DATE,
      description   TEXT NOT NULL,
      requirements  JSONB NOT NULL DEFAULT '[]',
      contact_email TEXT NOT NULL,
      tags          JSONB NOT NULL DEFAULT '[]',
      status        TEXT NOT NULL DEFAULT 'pending',
      created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
    );
  `)

  await query(`
    CREATE TABLE IF NOT EXISTS shortlists (
      user_id    TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
      job_id     TEXT NOT NULL REFERENCES jobs(id) ON DELETE CASCADE,
      created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
      PRIMARY KEY (user_id, job_id)
    );
  `)
}
