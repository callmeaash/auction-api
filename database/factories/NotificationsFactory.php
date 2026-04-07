<?php

namespace Database\Factories;

use App\Models\Notifications;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;
use App\NotificationType;

/**
 * @extends Factory<Notifications>
 */
class NotificationsFactory extends Factory
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
            'item_id' => Item::factory(),
            'type' => fake()->randomElement(\AppNotificationType::cases()),
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(10),
            'is_read' => fake()->boolean,
        ];
    }
}
