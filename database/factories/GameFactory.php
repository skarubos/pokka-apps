<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_mode_id' => 1,   // 固定
            'progress' => -1,      // 固定
            'result' => fake()->numberBetween(0, 5000),
            'ranking' => fake()->numberBetween(1, 5),
        ];
    }
}
