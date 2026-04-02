<?php

use App\Models\Item;
use App\Models\User;
use App\Models\Report;

it('prevents unauthenticated users from reporting items', function () {
    $item = Item::factory()->create();
    $response = $this->postJson("/api/v1/items/{$item->id}/reports", [
        'reason' => 'spam',
        'message' => 'This is spam',
    ]);
    $response->assertStatus(401);
});


it('prevents authenticated users from reporting their own items', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['user_id' => $user->id]);
    $response = $this->actingAs($user)->postJson("/api/v1/items/{$item->id}/reports", [
        'reason' => 'spam',
        'message' => 'This is spam',
    ]);
    $response->assertStatus(403)
            ->assertJsonPath('message', 'You cannot report your own item');
});


it('prevents authenticated users from reporting the same item twice', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Report::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
    ]);
    $response = $this->actingAs($user)->postJson("/api/v1/items/{$item->id}/reports", [
        'reason' => 'spam',
        'message' => 'This is spam',
    ]);
    $response->assertStatus(403)
            ->assertJsonPath('message', 'You have already reported this item');
});


it('prevents authenticated users from reporting items with invalid data', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $response = $this->actingAs($user)->postJson("/api/v1/items/{$item->id}/reports", [
        'reason' => '',
        'message' => 'This is spam',
    ]);
    $response->assertStatus(422)
            ->assertJsonPath('errors.reason.0', 'The reason field is required.');
});


it('allows authenticated users to report items with valid data', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $response = $this->actingAs($user)->postJson("/api/v1/items/{$item->id}/reports", [
        'reason' => 'spam',
        'message' => 'This is spam',
    ]);
    $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.reason', 'spam')
            ->assertJsonPath('data.message', 'This is spam');
});