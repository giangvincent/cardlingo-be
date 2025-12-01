<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\User;
use App\Models\UserMission;
use Illuminate\Support\Collection;

class MissionService
{
    public function __construct(protected XpService $xpService)
    {
    }

    /**
     * Update progress for active missions and return any newly completed.
     *
     * @param  array  $context e.g. ['type' => 'round_submitted', 'data' => [...]]
     * @return Collection<int, UserMission>
     */
    public function updateProgress(User $user, array $context): Collection
    {
        $now = now();

        $missions = Mission::query()
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->get();

        $completed = collect();

        foreach ($missions as $mission) {
            $userMission = UserMission::firstOrCreate(
                ['user_id' => $user->id, 'mission_id' => $mission->id],
                ['progress' => ['count' => 0]]
            );

            if ($userMission->completed_at) {
                continue;
            }

            if ($this->incrementProgress($mission, $userMission, $context)) {
                $userMission->completed_at = $now;
                $userMission->progress = $userMission->progress ?? [];
                $userMission->save();
                $this->reward($user, $mission);
                $completed->push($userMission);
            } else {
                $userMission->save();
            }
        }

        return $completed;
    }

    protected function incrementProgress(Mission $mission, UserMission $userMission, array $context): bool
    {
        $conditions = $mission->conditions ?? [];
        $data = $context['data'] ?? [];
        $progress = $userMission->progress ?? ['count' => 0];

        if (isset($conditions['type']) && ($context['type'] ?? null) !== $conditions['type']) {
            return false;
        }

        // Simple counter mission: reach target_count
        $progress['count'] = ($progress['count'] ?? 0) + 1;
        $userMission->progress = $progress;

        $target = $conditions['target_count'] ?? null;
        if ($target && $progress['count'] >= $target) {
            return true;
        }

        // Points-based mission: accumulate target_points
        if (isset($conditions['target_points'])) {
            $progress['points'] = ($progress['points'] ?? 0) + ($data['points'] ?? 0);
            $userMission->progress = $progress;
            return $progress['points'] >= $conditions['target_points'];
        }

        return false;
    }

    protected function reward(User $user, Mission $mission): void
    {
        if ($mission->reward_xp > 0) {
            $this->xpService->addXp($user, $mission->reward_xp, 'mission', $mission->id, [
                'key' => $mission->key,
            ]);
        }

        if ($mission->reward_coins > 0) {
            $profile = $user->profile ?? null;
            if ($profile) {
                $profile->increment('coins', $mission->reward_coins);
            }
        }
    }
}
