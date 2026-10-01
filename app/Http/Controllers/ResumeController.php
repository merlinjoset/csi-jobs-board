<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    public function show(Resume $resume)
    {
        $user = Auth::user();

        $isOwner = $resume->user_id === $user->id;

        // A provider may view a resume only if it was submitted in an application
        // to one of their own job posts.
        $providerCanView = $user->isProvider() && Application::where('resume_id', $resume->id)
            ->whereHas('jobPost', fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        abort_unless($isOwner || $providerCanView, 403, 'You are not allowed to view this resume.');
        abort_unless(Storage::exists($resume->path), 404, 'Resume file is missing.');

        return response()->file(Storage::path($resume->path), [
            'Content-Disposition' => 'inline; filename="' . $resume->original_name . '"',
        ]);
    }
}
