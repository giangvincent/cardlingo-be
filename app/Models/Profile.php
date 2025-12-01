<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'avatar',
        'locale',
        'current_level',
        'current_xp',
        'total_xp',
        'coins',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'current_level' => 'integer',
            'current_xp' => 'integer',
            'total_xp' => 'integer',
            'coins' => 'integer',
            'settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
