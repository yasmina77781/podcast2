<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Podcast;
use App\Models\Episode;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer 1 admin
        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@test.com',
            'role' => 'admin'
        ]);

        // 2. Créer 3 animateurs
        $animateurs = User::factory()->count(3)->create([
            'role' => 'animateur'
        ]);

        // 3. Créer 5 users normaux
        User::factory()->count(5)->create();

        // 4. Chaque animateur crée 2 podcasts
        foreach ($animateurs as $animateur) {
            $podcasts = Podcast::factory()->count(2)->create([
                'user_id' => $animateur->id
            ]);

            // 5. Chaque podcast a 3 épisodes
            foreach ($podcasts as $podcast) {
                Episode::factory()->count(3)->create([
                    'podcast_id' => $podcast->id
                ]);
            }
        }
    }
}
