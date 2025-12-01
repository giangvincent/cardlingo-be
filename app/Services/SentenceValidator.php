<?php

namespace App\Services;

use App\Models\Card;
use Illuminate\Support\Collection;

class SentenceValidator
{
    /**
     * Evaluate a submitted round and return scoring + simple grammar report.
     *
     * @param  Collection<int, Card>  $cards
     */
    public function evaluate(Collection $cards, ?string $sentenceText, bool $usedOriginalCards): array
    {
        $baseScore = $cards->sum(fn (Card $card) => (int) $card->base_points);
        $bonusScore = $usedOriginalCards ? 5 : 0;

        $text = $sentenceText ?: $cards->pluck('text')->implode(' ');

        return [
            'text' => $text,
            'is_valid' => true,
            'grammar_report' => [
                'notes' => 'Basic validation placeholder',
            ],
            'base_score' => $baseScore,
            'bonus_score' => $bonusScore,
            'total_score' => $baseScore + $bonusScore,
        ];
    }
}
