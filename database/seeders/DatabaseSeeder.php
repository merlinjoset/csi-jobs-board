<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobPost;
use App\Models\Resume;
use App\Models\User;
use App\Services\ResumeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $resumes = new ResumeService();

        // ---- Providers ----
        $provider = User::create([
            'name' => 'Grace Staffing',
            'email' => 'provider@example.com',
            'password' => 'password',
            'role' => 'provider',
        ]);
        $provider2 = User::create([
            'name' => 'Gulf Star Trading',
            'email' => 'provider2@example.com',
            'password' => 'password',
            'role' => 'provider',
        ]);

        // ---- Jobs (skills drive resume matching) ----
        $jobs = [
            ['Junior PHP Developer', 'Gulf Star Trading', 'Dubai', 'Technology', 'Full-time', 'AED 6,000-8,000 / month', 'php, laravel, mysql, javascript, git', 'Build and maintain web apps with Laravel. Great for a developer early in their career.'],
            ['Frontend Developer', 'BrightApps', 'Remote', 'Technology', 'Full-time', 'AED 7,000-9,000 / month', 'javascript, react, html, css, tailwind', 'Craft responsive interfaces with React and Tailwind. Portfolio preferred.'],
            ['Office Administrator', 'Gulf Star Trading', 'Dubai', 'Administration', 'Full-time', 'AED 4,000-5,000 / month', 'excel, administration, data entry, scheduling, communication', 'Run the front office, manage schedules and correspondence.'],
            ['Retail Sales Associate', 'Home Centre', 'Mall of the Emirates', 'Sales & Retail', 'Full-time', 'AED 3,500-4,200 / month', 'sales, customer service, retail, cashier', 'Help customers, manage displays and checkout. Commission on top of base.'],
            ['Delivery Driver', 'Swift Logistics', 'Al Quoz', 'Logistics & Driving', 'Full-time', 'AED 3,000-3,800 / month', 'driving, logistics, warehouse', 'Daytime delivery routes across Dubai. Company vehicle provided.'],
            ['Staff Nurse', 'City Clinic', 'Deira', 'Healthcare', 'Full-time', 'AED 6,000-7,500 / month', 'nursing, first aid, caregiving', 'Provide patient care in a busy outpatient clinic.'],
            ['Accountant', 'Al Barsha Traders', 'Business Bay', 'Finance & Accounting', 'Full-time', 'AED 5,500-7,000 / month', 'accounting, tally, excel, payroll', 'Manage invoices, reconcile accounts, prepare monthly reports.'],
            ['Barista', 'Corner Cafe', 'JLT', 'Hospitality & Food', 'Part-time', 'AED 40-50 / hour', 'barista, customer service, cooking', 'Prepare coffee and serve customers with a smile.'],
        ];

        foreach ($jobs as $i => $j) {
            JobPost::create([
                'user_id' => $i % 3 === 0 ? $provider2->id : $provider->id,
                'title' => $j[0], 'company' => $j[1], 'location' => $j[2],
                'category' => $j[3], 'employment_type' => $j[4], 'salary' => $j[5],
                'skills' => $j[6], 'description' => $j[7], 'status' => 'open',
            ]);
        }

        // ---- Seekers + resumes ----
        $devSeeker = User::create([
            'name' => 'Sarah Mitchell', 'email' => 'seeker@example.com',
            'password' => 'password', 'role' => 'seeker', 'phone' => '+971 50 123 4567',
        ]);

        $devResumeText = "Sarah Mitchell\nWeb Developer\n\nSkills: PHP, Laravel, MySQL, JavaScript, React, HTML, CSS, Git.\n"
            . "Experience building web applications with Laravel and React. Comfortable with SQL databases and REST APIs.\n"
            . "Strong communication and time management.";
        $devResume = $this->seedResume($resumes, $devSeeker, 'sarah-mitchell-resume.txt', $devResumeText);

        $adminSeeker = User::create([
            'name' => 'John Raj', 'email' => 'seeker2@example.com',
            'password' => 'password', 'role' => 'seeker',
        ]);
        $adminResumeText = "John Raj\nOffice Administrator\n\nSkills: Excel, Administration, Data Entry, Scheduling, Customer Service.\n"
            . "Five years running busy offices, managing schedules and correspondence. Proficient in Microsoft Office.";
        $this->seedResume($resumes, $adminSeeker, 'john-raj-resume.txt', $adminResumeText);

        // ---- An application so the provider has someone to review ----
        $phpJob = JobPost::where('title', 'Junior PHP Developer')->first();
        Application::create([
            'job_post_id' => $phpJob->id,
            'user_id' => $devSeeker->id,
            'resume_id' => $devResume->id,
            'cover_note' => 'I have hands-on Laravel and React experience and would love to contribute.',
            'status' => 'applied',
        ]);
    }

    private function seedResume(ResumeService $resumes, User $user, string $name, string $text): Resume
    {
        $path = 'resumes/' . $name;
        Storage::disk('local')->put($path, $text);

        return Resume::create([
            'user_id' => $user->id,
            'original_name' => $name,
            'path' => $path,
            'mime' => 'text/plain',
            'parsed_text' => $text,
            'skills' => implode(', ', $resumes->detectSkills($text)),
        ]);
    }
}
