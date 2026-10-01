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
    ];

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
