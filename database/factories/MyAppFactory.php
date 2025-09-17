<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MyApp;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MyApps>
 */
class MyAppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    protected $model = MyApp::class;

    public function definition(): array
    {
        return [
            'name'       => ucfirst($this->faker->words(2, true)),
            'url'        => $this->faker->url(),
            'explanation'=> $this->faker->sentence(),
            // sort_order は Seeder 側で id と同じ値に設定するため、ここでは仮の値
            'sort_order' => 0,
            'type' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
