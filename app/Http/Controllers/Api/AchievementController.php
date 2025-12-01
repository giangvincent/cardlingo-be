<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Achievement::query()->orderBy('name')->get()
        );
    }

    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $user->load('achievements.achievement');

        return response()->json($user->achievements);
    }
}
