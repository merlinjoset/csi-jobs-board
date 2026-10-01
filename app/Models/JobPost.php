<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'company',
        'location',
        'employment_type',
        'category',
        'salary',
        'description',
        'skills',
        'status',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class)->latest();
    }

    // Normalised list of skill keywords for this posting.
    public function skillList(): array
    {
        return collect(preg_split('/[,\n]+/', (string) $this->skills))
            ->map(fn ($s) => strtolower(trim($s)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
