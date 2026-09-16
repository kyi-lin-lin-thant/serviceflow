<?php

use App\Models\User;

test('guest cannot access the current user endpoint', function () {
    $response = $this->getJson('/api/user');

    $response->assertUnauthorized();
});

test('authenticated user can access the current user endpoint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/user');

    $response->assertOk()
        ->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
});
