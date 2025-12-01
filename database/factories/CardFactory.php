<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Card>
 */
class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        $types = ['noun', 'verb', 'location', 'time', 'adj', 'adverb', 'prep', 'conj', 'extra', 'wild'];
        $type = fake()->randomElement($types);

        return [
            'deck_id' => Deck::factory(),
            'code' => strtoupper(fake()->bothify('??#')),
            'type' => $type,
            'text' => fake()->word(),
            'image_path' => null,
            'base_points' => fake()->numberBetween(1, 5),
            'grammar_metadata' => [
                'allowed_tenses' => fake()->randomElements(['present', 'past', 'future'], 2),
                'singular' => fake()->boolean(),
            ],
            'is_active' => true,
        ];
    }
}
