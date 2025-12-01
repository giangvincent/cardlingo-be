<?php

namespace Database\Factories;

use App\Models\Deck;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Deck>
 */
class DeckFactory extends Factory
{
    protected $model = Deck::class;

    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->word()) . ' Deck';

        return [
            'key' => 'deck_' . Str::slug($name . '_' . fake()->unique()->word()),
            'name' => $name,
            'description' => fake()->sentence(8),
            'difficulty_level' => fake()->randomElement(['starter', 'intermediate', 'advanced']),
            'is_premium' => fake()->boolean(10),
            'is_active' => true,
        ];
    }
}
