<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'deck_id',
        'mode',
        'status',
        'current_round',
        'max_rounds',
        'total_score',
        'started_at',
        'finished_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'current_round' => 'integer',
            'max_rounds' => 'integer',
            'total_score' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    public function sentences(): HasMany
    {
        return $this->hasMany(Sentence::class);
    }
}
