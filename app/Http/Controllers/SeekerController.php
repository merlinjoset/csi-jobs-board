<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use App\Models\Resume;
use App\Services\ResumeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SeekerController extends Controller
{
    public function __construct(private ResumeService $resumes)
    {
    }

    private function ensureSeeker(): void
    {
        abort_unless(Auth::user()?->isSeeker(), 403, 'This area is for job seekers.');
    }

    public function dashboard()
    {
        $this->ensureSeeker();
        $user = Auth::user();
        $resume = $user->latestResume();

        $suggestions = $resume ? $this->resumes->suggestJobs($resume) : [];
        $applications = $user->applications()->with('jobPost')->take(5)->get();

        return view('seeker.dashboard', compact('user', 'resume', 'suggestions', 'applications'));
    }

    public function uploadResume(Request $request)
    {
        $this->ensureSeeker();

        $request->validate([
            'resume' => ['required', 'file', 'mimes:pdf,docx,txt', 'max:5120'],
        ]);

        $file = $request->file('resume');
        $path = $file->store('resumes');
        $absolute = Storage::path($path);

        $text = $this->resumes->extractText($absolute, $file->getClientMimeType(), $file->getClientOriginalName());
        $skills = $this->resumes->detectSkills($text);

        Resume::create([
            'user_id' => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'parsed_text' => mb_substr($text, 0, 60000),
            'skills' => implode(', ', $skills),
        ]);

        $msg = $skills
            ? 'Resume uploaded. We detected: ' . implode(', ', $skills) . '. See your suggested jobs below.'
            : 'Resume uploaded, but we could not detect known skills. You can still browse and apply.';

        return redirect()->route('dashboard')->with('status', $msg);
    }

    public function apply(Request $request, JobPost $job)
    {
        $this->ensureSeeker();
        $user = Auth::user();

        $resume = $user->latestResume();
        if (! $resume) {
            return redirect()->route('dashboard')->with('error', 'Please upload your resume before applying.');
        }

        if (Application::where('job_post_id', $job->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'You have already applied to this job.');
        }

        Application::create([
            'job_post_id' => $job->id,
            'user_id' => $user->id,
            'resume_id' => $resume->id,
            'cover_note' => $request->input('cover_note'),
            'status' => 'applied',
        ]);

        return redirect()->route('jobs.show', $job)->with('status', 'Application submitted with your latest resume.');
    }

    public function applications()
    {
        $this->ensureSeeker();
        $applications = Auth::user()->applications()->with('jobPost.provider')->get();

        return view('seeker.applications', compact('applications'));
    }
}
