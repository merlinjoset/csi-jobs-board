# CSI Tamil Parish Dubai — Jobs Board (Prototype)

A community **referral job board** for the parish. Members share job openings
they know about — at their workplace, their own business, or through their
network — to help fellow members find good work. It is *not* the church's own
staff vacancies.

Built with **Vite + React + Tailwind CSS** on the front end and an
**Express + PostgreSQL (Neon)** API on the back end.

## Architecture

```
Frontend (Vite/React, :5173)  ──/api──▶  Express API (:3001)  ──▶  Neon Postgres
   src/                                    server/
```

The Vite dev server proxies `/api/*` to the Express server (see `vite.config.js`).

## Getting started

You need **two** processes running.

**1. Backend** (first time, create the tables + seed data):

```bash
cd server
npm install
npm run init-db      # creates users / jobs / shortlists tables and seeds them
npm start            # API on http://localhost:3001
```

**2. Frontend** (in a second terminal):

```bash
npm install
npm run dev          # app on http://localhost:5173
```

Open http://localhost:5173.

### Configuration

The database connection lives in `server/.env`:

```
DATABASE_URL=postgres://…   # Neon connection string (sslmode=require)
PORT=3001
```

`server/.env` is gitignored. **The current credential was shared in plaintext —
rotate it in the Neon console before any real use.**

## Demo accounts

| Role   | Email                              | Password    |
| ------ | ---------------------------------- | ----------- |
| Admin  | `admin@csitamilparishdubai.com`    | `admin123`  |
| Member | `member@csitamilparishdubai.com`   | `member123` |

## Features

- **Browse** — searchable, filterable board of approved openings.
- **Share an opportunity** — signed-in members post an opening; it arrives as
  **pending** and only appears once an admin approves it.
- **Job detail** — full posting, the member's referral note, and an
  "Express interest" action revealing the contact email.
- **Shortlist** — signed-in members bookmark jobs; saved per user in the DB.
- **Admin dashboard** — two tabs:
  - **Postings** — monitor every posting: stats, a review queue
    (Approve / Reject), and **Close / Reopen / Remove** for live postings.
    Closing keeps the record but drops it from the public board.
  - **Users** — manage accounts: view members/admins with activity counts,
    add a user, change roles (member ↔ admin), and remove accounts. Guards
    prevent demoting/deleting the last admin or deleting your own account.

## Database schema

- `users(id, name, email, password, role)`
- `jobs(id, title, org, posted_by, shared_note, category, type, location, pay,
  posted_at, description, requirements, contact_email, tags, status, created_at)`
- `shortlists(user_id, job_id, created_at)`

Job `status` is one of `pending | approved | rejected | closed`. The public
board shows only `approved`.

## Prototype notes / next steps

Passwords are stored in plaintext and prototype auth uses a simple user-id
header — fine for a demo, **not** production. Before going live you'd want:

- Hashed passwords + real sessions/JWT and self-serve member sign-up
- Email/in-app notifications when someone expresses interest
- Posting expiry and abuse reporting
- Deployment (e.g. API on Render/Fly, static frontend on Netlify/Vercel)
