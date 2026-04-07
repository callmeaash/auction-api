<?php

use App\Models\Notifications;
use App\Models\User;

it('can fetch all notifications', function () {
    $user = User::factory()->create();
    Notifications::factory(3)->create(['user_id' => $user->id]);
    $response = $this->actingAs($user)->get('/api/v1/notifications');
    $response->assertStatus(200);
    $response->assertJsonPath('message', 'Notifications fetched successfully');
    $response->assertJsonCount(3, 'data');
});

it('can mark a notification as read', function () {
    $user = User::factory()->create();
    $notification = Notifications::factory()->create(['user_id' => $user->id]);
    $response = $this->actingAs($user)->patch('/api/v1/notifications/' . $notification->id . '/read');
    $response->assertStatus(200);
    $response->assertJsonPath('message', 'Notification marked as read');
    $response->assertJsonPath('data.is_read', true);
});

it('can mark all notifications as read', function () {
    $user = User::factory()->create();
    Notifications::factory(5)->create(['user_id' => $user->id, 'is_read' => false] );
    $response = $this->actingAs($user)->patch('/api/v1/notifications/read-all');
    $response->assertStatus(200);
    $response->assertJsonPath('message', '5 notifications marked as read');
    $response->assertJsonCount(5, 'data');
});

it('can delete a notification', function () {
    $user = User::factory()->create();
    $notification = Notifications::factory()->create(['user_id' => $user->id]);
    $response = $this->actingAs($user)->delete('/api/v1/notifications/' . $notification->id);
    $response->assertStatus(200);
    $response->assertJsonPath('message', 'Notification deleted successfully');
});

it('can delete all notifications', function () {
    $user = User::factory()->create();
    Notifications::factory(5)->create(['user_id' => $user->id]);
    $response = $this->actingAs($user)->delete('/api/v1/notifications/delete-all');
    $response->assertStatus(200);
    $response->assertJsonPath('message', 'All notifications deleted successfully');
    $response->assertJsonCount(0, 'data');
});


