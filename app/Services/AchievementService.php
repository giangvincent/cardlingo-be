<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Collection;

class AchievementService
{
    public function __construct(protected XpService $xpService)
    {
    }

    /**
     * Evaluate all achievements for the given context and return any newly unlocked.
     *
     * @param  array  $context e.g. ['type' => 'round_submitted', 'data' => [...]]
     * @return Collection<int, UserAchievement>
     */
    public function checkAll(User $user, array $context): Collection
    {
        $existingAchievementIds = $user->achievements()->pluck('achievement_id')->all();

        $unlocked = collect();

        Achievement::query()
            ->whereNotIn('id', $existingAchievementIds)
            ->get()
            ->each(function (Achievement $achievement) use ($user, $context, $unlocked) {
                if ($this->meetsConditions($achievement, $context)) {
                    $userAchievement = UserAchievement::create([
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                        'unlocked_at' => now(),
                    ]);

                    $this->reward($user, $achievement);

                    $unlocked->push($userAchievement);
                }
            });

        return $unlocked;
    }

    protected function meetsConditions(Achievement $achievement, array $context): bool
    {
        $conditions = $achievement->conditions ?? [];
        $data = $context['data'] ?? [];

        if (isset($conditions['type']) && ($context['type'] ?? null) !== $conditions['type']) {
            return false;
        }

        if (isset($conditions['min_points']) && ($data['points'] ?? 0) < $conditions['min_points']) {
            return false;
        }

        if (isset($conditions['require_original_cards']) && $conditions['require_original_cards'] === true) {
            if (!($data['used_original_cards'] ?? false)) {
                return false;
            }
        }

        if (isset($conditions['require_mode']) && ($data['mode'] ?? null) !== $conditions['require_mode']) {
            return false;
        }

        return true;
    }

    protected function reward(User $user, Achievement $achievement): void
    {
        if ($achievement->reward_xp > 0) {
            $this->xpService->addXp($user, $achievement->reward_xp, 'achievement', $achievement->id, [
                'key' => $achievement->key,
            ]);
        }

        if ($achievement->reward_coins > 0) {
            $profile = $user->profile ?? null;
            if ($profile) {
                $profile->increment('coins', $achievement->reward_coins);
            }
        }
    }
}
