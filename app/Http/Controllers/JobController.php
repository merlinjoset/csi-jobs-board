<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public const CATEGORIES = [
        'Administration', 'Technology', 'Sales & Retail', 'Customer Service',
        'Construction & Trades', 'Logistics & Driving', 'Healthcare',
        'Hospitality & Food', 'Education', 'Finance & Accounting', 'Other',
    ];

    public const TYPES = ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship'];

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = $request->query('category', '');
        $type = $request->query('type', '');

        $jobs = JobPost::with('provider')
            ->where('status', 'open')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('company', 'like', "%{$q}%")
                        ->orWhere('skills', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($type !== '', fn ($query) => $query->where('employment_type', $type))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
            'q' => $q,
            'category' => $category,
            'type' => $type,
            'categories' => self::CATEGORIES,
            'types' => self::TYPES,
        ]);
    }

    public function show(JobPost $job)
    {
        $job->load('provider');

        $alreadyApplied = false;
        if ($user = Auth::user()) {
            $alreadyApplied = Application::where('job_post_id', $job->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        return view('jobs.show', compact('job', 'alreadyApplied'));
    }
}
