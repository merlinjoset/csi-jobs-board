<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationMail;
use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProviderController extends Controller
{
    private function ensureProvider(): void
    {
        abort_unless(Auth::user()?->isProvider(), 403, 'This area is for job providers.');
    }

    public function dashboard()
    {
        $this->ensureProvider();
        $jobs = Auth::user()->jobPosts()->withCount('applications')->latest()->get();

        return view('provider.dashboard', compact('jobs'));
    }

    public function create()
    {
        $this->ensureProvider();

        return view('provider.create', [
            'categories' => JobController::CATEGORIES,
            'types' => JobController::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureProvider();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:' . implode(',', JobController::TYPES)],
            'category' => ['required', 'in:' . implode(',', JobController::CATEGORIES)],
            'salary' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'skills' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'open';
        JobPost::create($data);

        return redirect()->route('provider.dashboard')->with('status', 'Job posted. Seekers can now find and apply to it.');
    }

    public function toggle(JobPost $job)
    {
        $this->ensureProvider();
        abort_unless($job->user_id === Auth::id(), 403);

        $job->update(['status' => $job->status === 'open' ? 'closed' : 'open']);

        return back()->with('status', "Job marked {$job->status}.");
    }

    // Everyone who applied to one of this provider's jobs, with their resume.
    public function applicants(JobPost $job)
    {
        $this->ensureProvider();
        abort_unless($job->user_id === Auth::id(), 403);

        $applications = $job->applications()->with(['seeker', 'resume'])->get();

        return view('provider.applicants', compact('job', 'applications'));
    }

    public function updateApplication(Request $request, Application $application)
    {
        $this->ensureProvider();
        abort_unless($application->jobPost->user_id === Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:applied,shortlisted,rejected'],
            'provider_message' => ['nullable', 'string', 'max:1000'],
        ]);
        $application->update($data);

        // Email the seeker when they are shortlisted or rejected.
        if (in_array($data['status'], ['shortlisted', 'rejected'], true)) {
            try {
                Mail::to($application->seeker->email)->send(new ApplicationMail($application, $data['status']));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', 'Applicant updated. They will see your response (and get an email).');
    }

    public function scheduleInterview(Request $request, Application $application)
    {
        $this->ensureProvider();
        abort_unless($application->jobPost->user_id === Auth::id(), 403);

        $data = $request->validate([
            'interview_at' => ['required', 'date', 'after:now'],
            'interview_mode' => ['required', 'in:In-person,Online,Phone'],
            'interview_location' => ['nullable', 'string', 'max:255'],
            'interview_note' => ['nullable', 'string', 'max:1000'],
        ]);
        $data['status'] = 'interview';
        $application->update($data);

        try {
            Mail::to($application->seeker->email)->send(new ApplicationMail($application, 'interview'));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Interview scheduled. The applicant will see the details and receive an email.');
    }
}
