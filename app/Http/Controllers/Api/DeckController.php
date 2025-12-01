<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class DeckController extends Controller
{
    public function index(): JsonResponse
    {
        $decks = Cache::remember('decks.active', 300, function () {
            return Deck::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        return response()->json($decks);
    }

    public function cards(Deck $deck): JsonResponse
    {
        abort_unless($deck->is_active, 404);

        $deckWithCards = Cache::remember("deck.{$deck->id}.cards", 300, function () use ($deck) {
            return $deck->load(['cards' => function ($query) {
                $query->where('is_active', true)->orderBy('id');
            }]);
        });

        return response()->json($deckWithCards);
    }
}
