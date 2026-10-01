# CSI Job Portal (Laravel)

A job portal with two roles, resume upload, and resume-based job suggestions.
Built with **Laravel 13**, **Blade + Tailwind**, and **MariaDB (MySQL)**, managed
through **phpMyAdmin**.

## Roles

- **Job Seeker**: anyone can register, upload a resume (PDF / DOCX / TXT), and the
  portal parses it, detects skills, and suggests the best-matching jobs. Seekers
  apply to jobs with their latest resume.
- **Job Provider**: registers, posts jobs (with skills), and sees every applicant
  for their jobs, including each applicant's resume and detected skills. Providers
  can shortlist or reject.

## How matching works

On upload, `App\Services\ResumeService` extracts text (smalot/pdfparser for PDF,
ZipArchive for DOCX, plain read for TXT), detects known skills, and stores them.
Suggestions score each open job by skill overlap (3 points per shared skill) plus
keyword hits from the job title/category/skills found in the resume text.

## Running it

Prerequisites already set up on this machine: PHP 8.4 (with a `php.ini` enabling
pdo_mysql, openssl, mbstring, zip, gd, curl), Composer, and a MariaDB service.

```powershell
# from csi-portal/
php artisan migrate:fresh --seed   # build + seed the database (csi_portal)
php artisan serve --port=8010      # app at http://127.0.0.1:8010
```

- **App**: http://127.0.0.1:8010
- **phpMyAdmin**: http://127.0.0.1:8081 (login root / root)
- **Database**: MariaDB `csi_portal` on 127.0.0.1:3306 (root / root), see `.env`

## Demo accounts (password: `password`)

| Role     | Email                   |
| -------- | ----------------------- |
| Provider | provider@example.com    |
| Provider | provider2@example.com   |
| Seeker   | seeker@example.com      |
| Seeker   | seeker2@example.com     |

## Key files

- `app/Models/` — User, JobPost, Resume, Application
- `app/Services/ResumeService.php` — text extraction, skill detection, matching
- `app/Http/Controllers/` — Auth, Job (public), Seeker, Provider, Resume
- `database/migrations/2026_10_01_120000_create_portal_schema.php` — schema
- `database/seeders/DatabaseSeeder.php` — demo data
- `resources/views/` — Blade views (layouts, auth, jobs, seeker, provider)

## Notes

- Tailwind is loaded via the Play CDN for convenience; swap for a built asset
  pipeline before production.
- Resume files are stored privately under `storage/app/private/resumes` and served
  only to the owning seeker or a provider who received that resume as an application.
