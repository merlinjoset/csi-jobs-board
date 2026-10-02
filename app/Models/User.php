<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'headline',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isSeeker(): bool
    {
        return $this->role === 'seeker';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Jobs posted by this provider.
    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    // Resumes uploaded by this seeker.
    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class)->latest();
    }

    public function latestResume(): ?Resume
    {
        return $this->resumes()->first();
    }

    // Applications submitted by this seeker.
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class)->latest();
    }
}
