<?php

namespace Database\Seeders;

use App\Models\Deck;
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
    }
}
