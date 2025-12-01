<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use App\Models\XpEvent;
use Illuminate\Support\Facades\DB;

class XpService
{
    public function addXp(User $user, int $amount, string $sourceType, ?int $sourceId = null, array $meta = []): array
    {
        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $meta) {
            /** @var Profile $profile */
            $profile = $user->profile ?? $this->createProfileForUser($user);

            XpEvent::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'meta' => $meta,
            ]);

            $progression = app(ProgressionService::class)->apply($profile, $amount);

            return [
                'profile' => $profile->fresh(),
                'progression' => $progression,
            ];
        });
    }

    protected function createProfileForUser(User $user): Profile
    {
        return Profile::create([
            'user_id' => $user->id,
            'display_name' => $user->name,
            'locale' => 'en',
        ]);
    }
}
