<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sentence extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'user_id',
        'cards',
        'text',
        'is_valid',
        'grammar_report',
        'base_score',
        'bonus_score',
        'total_score',
    ];

    protected function casts(): array
    {
        return [
            'cards' => 'array',
            'grammar_report' => 'array',
            'is_valid' => 'boolean',
            'base_score' => 'integer',
            'bonus_score' => 'integer',
            'total_score' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'game_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): HasOne
    {
        return $this->hasOne(Round::class);
    }
}
