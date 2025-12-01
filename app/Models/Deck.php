<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'difficulty_level',
        'is_premium',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Deck $deck) {
            cache()->forget('decks.active');
            cache()->forget("deck.{$deck->id}.cards");
        });

        static::deleted(function (Deck $deck) {
            cache()->forget('decks.active');
            cache()->forget("deck.{$deck->id}.cards");
        });
    }
}
