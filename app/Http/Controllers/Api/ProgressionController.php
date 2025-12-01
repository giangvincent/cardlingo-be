<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProgressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressionController extends Controller
{
    public function __construct(
        protected ProgressionService $progressionService
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $profile = $user->profile ?? null;
        abort_unless($profile, 404, 'Profile not found.');

        return response()->json($this->progressionService->snapshot($profile));
    }
}
