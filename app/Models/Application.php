<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'job_post_id',
        'user_id',
        'resume_id',
        'cover_note',
        'status',
        'provider_message',
        'interview_at',
        'interview_mode',
        'interview_location',
        'interview_note',
    ];

    protected function casts(): array
    {
        return ['interview_at' => 'datetime'];
    }

    public function hasInterview(): bool
    {
        return $this->status === 'interview' && $this->interview_at !== null;
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function seeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
