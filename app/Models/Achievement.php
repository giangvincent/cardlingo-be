<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'reward_xp',
        'reward_coins',
        'conditions',
    ];

    protected function casts(): array
    {
        return [
            'reward_xp' => 'integer',
            'reward_coins' => 'integer',
            'conditions' => 'array',
        ];
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }
}
