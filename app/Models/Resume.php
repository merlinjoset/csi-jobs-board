<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resume extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'path',
        'mime',
        'parsed_text',
        'skills',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
