<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function daily(Request $request): JsonResponse
    {
        return $this->missionsByType('daily', $request);
    }

    public function weekly(Request $request): JsonResponse
    {
        return $this->missionsByType('weekly', $request);
    }

    protected function missionsByType(string $type, Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $missions = Mission::query()
            ->where('type', $type)
            ->orderBy('start_date')
            ->get();

        $userMissions = $user->missions()->with('mission')->get()->keyBy('mission_id');

        $payload = $missions->map(function (Mission $mission) use ($userMissions) {
            $userMission = $userMissions->get($mission->id);

            return [
                'mission' => $mission,
                'progress' => $userMission?->progress,
                'completed_at' => $userMission?->completed_at,
            ];
        });

        return response()->json($payload);
    }
}
