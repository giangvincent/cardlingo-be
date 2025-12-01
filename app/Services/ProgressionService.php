<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\UserUnlock;

class ProgressionService
{
    /**
     * Apply XP to a profile, handle level-ups, and return an array snapshot.
     */
    public function apply(Profile $profile, int $amount): array
    {
        $profile->current_xp += $amount;
        $profile->total_xp += $amount;

        $unlocks = [];

        while ($profile->current_xp >= $this->xpForNextLevel($profile->current_level)) {
            $profile->current_xp -= $this->xpForNextLevel($profile->current_level);
            $profile->current_level++;

            // Placeholder unlock rule: every level unlocks a "level_{n}" cosmetic.
            $unlockKey = 'level_'.$profile->current_level;
            $unlock = UserUnlock::firstOrCreate([
                'user_id' => $profile->user_id,
                'unlockable_type' => 'cosmetic',
                'unlockable_key' => $unlockKey,
            ], [
                'unlocked_at' => now(),
            ]);

            $unlocks[] = $unlock;
        }

        $profile->save();

        return [
            'current_level' => $profile->current_level,
            'current_xp' => $profile->current_xp,
            'total_xp' => $profile->total_xp,
            'xp_for_next_level' => $this->xpForNextLevel($profile->current_level),
            'unlocks' => $unlocks,
        ];
    }

    public function xpForNextLevel(int $level): int
    {
        return max(50, $level * 100);
    }

    public function snapshot(Profile $profile): array
    {
        return [
            'current_level' => $profile->current_level,
            'current_xp' => $profile->current_xp,
            'total_xp' => $profile->total_xp,
            'xp_for_next_level' => $this->xpForNextLevel($profile->current_level),
            'unlocks' => UserUnlock::query()
                ->where('user_id', $profile->user_id)
                ->get(),
        ];
    }
}
