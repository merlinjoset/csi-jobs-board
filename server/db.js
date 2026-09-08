import pg from "pg"
import dotenv from "dotenv"

dotenv.config()

if (!process.env.DATABASE_URL) {
  throw new Error("DATABASE_URL is not set. Create server/.env with your connection string.")
}

// Neon requires SSL. The pooled connection string uses sslmode=require;
// we set ssl explicitly so the pg driver trusts the endpoint.
export const pool = new pg.Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: { rejectUnauthorized: false },
})

export function query(text, params) {
  return pool.query(text, params)
}
