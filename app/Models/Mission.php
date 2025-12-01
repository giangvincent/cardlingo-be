<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'key',
        'name',
        'description',
        'conditions',
        'reward_xp',
        'reward_coins',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'reward_xp' => 'integer',
            'reward_coins' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function userMissions(): HasMany
    {
        return $this->hasMany(UserMission::class);
    }
}
