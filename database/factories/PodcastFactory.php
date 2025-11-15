<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PodcastFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->text(200),
            'image_url' => 'https://via.placeholder.com/300',
            'genre' => fake()->randomElement(['Tech', 'Santé', 'Sport', 'Culture', 'Business']),
        ];
    }
}