<?php

use App\Models\Item;
use App\Models\User;

it('adds item to wishlist ', function() {
    $item = Item::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)
                    ->postJson("/api/v1/items/{$item->id}/wishlist");

    $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Item added to wishlist successfully');

    $this->assertDatabaseHas('wishlists', [
        'item_id' => $item->id,
        'user_id' => $user->id,
    ]);
});

it('removes item from wishlist', function() {
    $item = Item::factory()->create();
    $user = User::factory()->create();

    $user->wishlists()->toggle($item->id);

    $response = $this->actingAs($user)
                    ->postJson("/api/v1/items/{$item->id}/wishlist");

    $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Item removed from wishlist successfully');

    $this->assertDatabaseMissing('wishlists', [
        'item_id' => $item->id,
        'user_id' => $user->id,
    ]);
});

it('cannot add item to wishlist if not authenticated', function() {
    $item = Item::factory()->create();

    $response = $this->postJson("/api/v1/items/{$item->id}/wishlist");

    $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Unauthorized');
});