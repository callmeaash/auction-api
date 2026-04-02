<?php

use App\Models\User;
use App\Models\Item;
use App\Models\Bid;
use App\Models\Wishlist;

it('prevents unauthenticated users from accessing profile endpoints', function () {
    $response = $this->getJson('/api/v1/profile/items');
    $response->assertStatus(401);
});

it('fetches items created by authenticated user', function () {
    $user = User::factory()->create();
    $items = Item::factory(10)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
                    ->getJson('/api/v1/profile/items');

    $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pagination.total', 10);
});

it('fetches items bid on by authenticated user', function () {
    $user = User::factory()->create();
    $items = Item::factory(10)->create();
    $bids = Bid::factory(10)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
                    ->getJson('/api/v1/profile/bids');

    $response->assertStatus(200)
            ->assertJsonPath('success', true);
});

it('fetches items in authenticated user\'s wishlist', function () {
    $user = User::factory()->create();
    $items = Item::factory(10)->create();
    $wishlists = Wishlist::factory(10)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
                    ->getJson('/api/v1/profile/wishlist');

    $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pagination.total', 10);
});