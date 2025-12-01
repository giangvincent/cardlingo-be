<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\GameSession;
use App\Models\Round;
use App\Models\Sentence;
use App\Services\AchievementService;
use App\Services\MissionService;
use App\Services\SentenceValidator;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GameSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'deck_id' => ['nullable', 'integer', 'exists:decks,id'],
            'mode' => ['sometimes', 'string', 'max:50'],
            'max_rounds' => ['sometimes', 'integer', 'min:1'],
            'settings' => ['sometimes', 'array'],
        ]);

        $session = GameSession::create([
            'user_id' => optional($request->user())->id,
            'deck_id' => $data['deck_id'] ?? null,
            'mode' => $data['mode'] ?? 'standard',
            'status' => 'in_progress',
            'current_round' => 1,
            'max_rounds' => $data['max_rounds'] ?? 10,
            'total_score' => 0,
            'started_at' => now(),
            'settings' => $data['settings'] ?? null,
        ]);

        return response()->json($session->load('deck'), 201);
    }

    public function show(GameSession $gameSession, Request $request): JsonResponse
    {
        $this->ensureSessionOwner($gameSession, optional($request->user())->id);

        $gameSession->load([
            'deck',
            'rounds.sentence',
            'sentences',
        ]);

        return response()->json($gameSession);
    }

    public function submitRound(
        GameSession $gameSession,
        Request $request,
        SentenceValidator $validator,
        XpService $xpService,
        AchievementService $achievementService,
        MissionService $missionService
    ): JsonResponse {
        $this->ensureSessionOwner($gameSession, optional($request->user())->id);
        abort_if($gameSession->status === 'finished', 422, 'Session already finished.');

        $data = $request->validate([
            'cards' => ['required', 'array', 'min:1'],
            'cards.*' => ['integer', Rule::exists('cards', 'id')],
            'sentenceText' => ['nullable', 'string'],
            'used_original_cards' => ['sometimes', 'boolean'],
        ]);

        $cardModels = Card::query()->whereIn('id', $data['cards'])->get();
        if ($cardModels->count() !== count($data['cards'])) {
            abort(422, 'Some cards were not found.');
        }

        if ($gameSession->deck_id) {
            $invalid = $cardModels->firstWhere('deck_id', '!=', $gameSession->deck_id);
            if ($invalid) {
                abort(422, 'Cards must belong to the session deck.');
            }
        }

        $usedOriginalCards = $data['used_original_cards'] ?? false;
        $evaluation = $validator->evaluate($cardModels, $data['sentenceText'] ?? null, $usedOriginalCards);

        $sentence = Sentence::create([
            'game_session_id' => $gameSession->id,
            'user_id' => $gameSession->user_id,
            'cards' => $cardModels->pluck('id')->all(),
            'text' => $evaluation['text'],
            'is_valid' => $evaluation['is_valid'],
            'grammar_report' => $evaluation['grammar_report'],
            'base_score' => $evaluation['base_score'],
            'bonus_score' => $evaluation['bonus_score'],
            'total_score' => $evaluation['total_score'],
        ]);

        $round = Round::updateOrCreate(
            [
                'game_session_id' => $gameSession->id,
                'round_number' => $gameSession->current_round,
            ],
            [
                'score' => $evaluation['total_score'],
                'used_original_cards' => $usedOriginalCards,
                'sentence_id' => $sentence->id,
            ]
        );

        $gameSession->total_score += $evaluation['total_score'];
        $gameSession->status = 'in_progress';

        if ($gameSession->current_round >= $gameSession->max_rounds) {
            $gameSession->status = 'finished';
            $gameSession->current_round = $gameSession->max_rounds;
            $gameSession->finished_at = $gameSession->finished_at ?? now();
        } else {
            $gameSession->current_round++;
        }

        $gameSession->save();

        $responseData = $gameSession->load(['deck', 'rounds.sentence', 'sentences']);

        if ($request->user()) {
            $xpAward = $evaluation['total_score'];
            $context = [
                'type' => 'round_submitted',
                'data' => [
                    'points' => $evaluation['total_score'],
                    'used_original_cards' => $usedOriginalCards,
                    'mode' => $gameSession->mode,
                ],
            ];
            $progression = $xpService->addXp(
                $request->user(),
                $xpAward,
                'round',
                $round->id,
                ['mode' => $gameSession->mode, 'game_session_id' => $gameSession->id]
            );
            $responseData->setAttribute('progression', $progression['progression']);

            $newAchievements = $achievementService->checkAll($request->user(), $context);
            $missionCompletions = $missionService->updateProgress($request->user(), $context);

            $responseData->setAttribute('achievements_unlocked', $newAchievements);
            $responseData->setAttribute('missions_completed', $missionCompletions);
        }

        return response()->json($responseData);
    }

    public function finish(GameSession $gameSession): JsonResponse
    {
        $this->ensureSessionOwner($gameSession, optional(request()->user())->id);
        if ($gameSession->status !== 'finished') {
            $gameSession->update([
                'status' => 'finished',
                'finished_at' => $gameSession->finished_at ?? now(),
            ]);
        }

        return response()->json($gameSession);
    }

    protected function ensureSessionOwner(GameSession $gameSession, ?int $authUserId): void
    {
        if ($gameSession->user_id && ! $authUserId) {
            abort(401, 'Authentication required for this session.');
        }

        if ($gameSession->user_id && $authUserId !== $gameSession->user_id) {
            abort(403, 'You cannot access this session.');
        }
    }
}
