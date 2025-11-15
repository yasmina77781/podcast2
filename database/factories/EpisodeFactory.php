<?php

namespace Database\Factories;

use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpisodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'podcast_id' => Podcast::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->text(150),
            'audio_url' => 'https://exemple.com/audio.mp3',
            'duration' => fake()->numberBetween(600, 3600),
            'published_at' => now(),
        ];
    }
}