<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => fake()->text(),
            'message' => fake()->text(),
            'status' => fake()->randomElement(['pending', 'resolved', 'rejected']),
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
        ];
    }
}
