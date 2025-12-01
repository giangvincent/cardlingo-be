<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'deck_id',
        'code',
        'type',
        'text',
        'image_path',
        'base_points',
        'grammar_metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_points' => 'integer',
            'grammar_metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Card $card) {
            cache()->forget("deck.{$card->deck_id}.cards");
        });

        static::deleted(function (Card $card) {
            cache()->forget("deck.{$card->deck_id}.cards");
        });
    }
}
