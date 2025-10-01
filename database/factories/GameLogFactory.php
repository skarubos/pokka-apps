<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameLog>
 */
class GameLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => fake()->numberBetween(1, 59),
            'q_lat' => fake()->latitude(),
            'q_lng' => fake()->longitude(),
            'a_lat' => fake()->latitude(),
            'a_lng' => fake()->longitude(),
            'distance' => fake()->randomFloat(3, 0, 10000),
            'score' => fake()->numberBetween(0, 1000),
        ];
    }
}
