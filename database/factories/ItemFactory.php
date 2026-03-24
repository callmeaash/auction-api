<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Category;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'winner_id' => null,
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(Category::cases())->value,
            'image' => fake()->imageUrl(),
            'starting_bid' => fake()->randomFloat(2, 10, 1000),
            'winning_bid' => null,
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ];
    }
}
