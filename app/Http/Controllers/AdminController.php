<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403, 'This area is for administrators.');
    }

    public function dashboard()
    {
        $this->ensureAdmin();

        $stats = [
            'users' => User::count(),
            'seekers' => User::where('role', 'seeker')->count(),
            'providers' => User::where('role', 'provider')->count(),
            'jobs' => JobPost::count(),
            'open_jobs' => JobPost::where('status', 'open')->count(),
            'applications' => Application::count(),
            'resumes' => Resume::count(),
        ];

        $recentJobs = JobPost::with('provider')->latest()->take(5)->get();
        $recentApplications = Application::with(['seeker', 'jobPost'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentApplications'));
    }

    public function users(Request $request)
    {
        $this->ensureAdmin();

        $role = $request->query('role', '');
        $users = User::withCount(['jobPosts', 'applications', 'resumes'])
            ->when($role !== '', fn ($q) => $q->where('role', $role))
            ->orderByRaw("FIELD(role,'admin','provider','seeker')")
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users', 'role'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $this->ensureAdmin();
        $request->validate(['role' => ['required', 'in:seeker,provider,admin']]);

        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot change your own admin role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('status', "Updated {$user->name} to {$user->role}.");
    }

    public function deleteUser(User $user)
    {
        $this->ensureAdmin();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete(); // cascades to their jobs, resumes, applications

        return back()->with('status', "Deleted {$name} and their related records.");
    }

    public function jobs(Request $request)
    {
        $this->ensureAdmin();

        $status = $request->query('status', '');
        $jobs = JobPost::with('provider')
            ->withCount('applications')
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.jobs', compact('jobs', 'status'));
    }

    public function toggleJob(JobPost $job)
    {
        $this->ensureAdmin();
        $job->update(['status' => $job->status === 'open' ? 'closed' : 'open']);

        return back()->with('status', "Job \"{$job->title}\" marked {$job->status}.");
    }

    public function deleteJob(JobPost $job)
    {
        $this->ensureAdmin();
        $title = $job->title;
        $job->delete();

        return back()->with('status', "Deleted job \"{$title}\".");
    }

    public function applications()
    {
        $this->ensureAdmin();
        $applications = Application::with(['seeker', 'jobPost.provider', 'resume'])
            ->latest()
            ->paginate(25);

        return view('admin.applications', compact('applications'));
    }
}
