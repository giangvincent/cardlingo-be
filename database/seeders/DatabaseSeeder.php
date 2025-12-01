<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Deck;
use App\Models\Mission;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Deck::factory()
            ->count(2)
            ->hasCards(10)
            ->create();

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        Profile::create([
            'user_id' => $admin->id,
            'display_name' => 'Admin',
            'locale' => 'en',
        ]);

        Achievement::create([
            'key' => 'present_pro',
            'name' => 'Present Pro',
            'description' => 'Score 30+ points in a round.',
            'reward_xp' => 100,
            'reward_coins' => 50,
            'conditions' => [
                'type' => 'round_submitted',
                'min_points' => 30,
            ],
        ]);

        Achievement::create([
            'key' => 'originalist',
            'name' => 'Originalist',
            'description' => 'Use original cards for bonus.',
            'reward_xp' => 50,
            'reward_coins' => 20,
            'conditions' => [
                'type' => 'round_submitted',
                'require_original_cards' => true,
            ],
        ]);

        Mission::create([
            'type' => 'daily',
            'key' => 'daily_rounds_3',
            'name' => 'Play 3 rounds',
            'description' => 'Complete 3 rounds today.',
            'conditions' => [
                'type' => 'round_submitted',
                'target_count' => 3,
            ],
            'reward_xp' => 75,
            'reward_coins' => 30,
            'start_date' => now()->startOfDay(),
            'end_date' => now()->endOfDay(),
        ]);

        Mission::create([
            'type' => 'weekly',
            'key' => 'weekly_points_300',
            'name' => 'Score 300 points',
            'description' => 'Earn 300 total points this week.',
            'conditions' => [
                'type' => 'round_submitted',
                'target_points' => 300,
            ],
            'reward_xp' => 200,
            'reward_coins' => 100,
            'start_date' => now()->startOfWeek(),
            'end_date' => now()->endOfWeek(),
        ]);
    }
}
