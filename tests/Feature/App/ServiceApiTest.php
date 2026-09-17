<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/

test('a valid service can be created', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $response = $this->actingAs($manager)
        ->postJson('/api/services', [
            'name' => 'Office Cleaning',
            'description' => 'Professional office cleaning service',
            'category_id' => $category->id,
            'price' => 75.00,
            'duration_minutes' => 180,
            'status' => 'active',
        ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Office Cleaning');

    $this->assertDatabaseHas('services', [
        'name' => 'Office Cleaning',
        'category_id' => $category->id,
    ]);
});

test('service creation fails when required fields are missing', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)
        ->postJson('/api/services', [
            'description' => 'Professional cleaning service',
            'price' => 75.00,
            'duration_minutes' => 180,
            'status' => 'active',
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'category_id',
        ]);
});

test('service creation fails with an invalid category', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)
        ->postJson('/api/services', [
            'name' => 'Deep Cleaning',
            'description' => 'Professional deep cleaning service',
            'category_id' => 9999,
            'price' => 100.00,
            'duration_minutes' => 180,
            'status' => 'active',
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'category_id',
        ]);
});

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/

test('a valid service can be updated', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional house cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $newCategory = ServiceCategory::factory()->create();

    $response = $this->actingAs($manager)
        ->putJson("/api/services/{$service->id}", [
            'name' => 'Premium House Cleaning',
            'description' => 'Deep professional cleaning',
            'category_id' => $newCategory->id,
            'price' => 80.00,
            'duration_minutes' => 180,
            'status' => 'inactive',
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'Service updated successfully.')
        ->assertJsonPath('data.name', 'Premium House Cleaning')
        ->assertJsonPath('data.category_id', $newCategory->id);

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Premium House Cleaning',
        'category_id' => $newCategory->id,
        'price' => 80.00,
        'duration_minutes' => 180,
        'status' => 'inactive',
    ]);
});

test('service update fails when required fields are missing', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $response = $this->actingAs($manager)
        ->putJson("/api/services/{$service->id}", [
            'price' => 80.00,
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'category_id',
            'duration_minutes',
            'status',
        ]);
});

test('service update fails with invalid data', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $response = $this->actingAs($manager)
        ->putJson("/api/services/{$service->id}", [
            'name' => 'House Cleaning',
            'description' => 'Professional cleaning service',
            'category_id' => 9999,
            'price' => -10,
            'duration_minutes' => 0,
            'status' => 'unknown',
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'category_id',
            'price',
            'duration_minutes',
            'status',
        ]);
});

test('updating a non-existent service returns not found', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $response = $this->actingAs($manager)
        ->putJson('/api/services/9999', [
            'name' => 'Updated Service',
            'description' => 'Updated description',
            'category_id' => $category->id,
            'price' => 80.00,
            'duration_minutes' => 120,
            'status' => 'active',
        ]);

    $response->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| PATCH
|--------------------------------------------------------------------------
*/

test('a service can be partially updated', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $response = $this->actingAs($manager)
        ->patchJson("/api/services/{$service->id}", [
            'price' => 75.00,
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'House Cleaning')
        ->assertJsonPath('data.description', 'Professional cleaning service')
        ->assertJsonPath('data.price', '75.00')
        ->assertJsonPath('data.duration_minutes', 120)
        ->assertJsonPath('data.status', 'active');

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'price' => 75.00,
        'status' => 'active',
    ]);
});

test('service patch fails with invalid data', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $response = $this->actingAs($manager)
        ->patchJson("/api/services/{$service->id}", [
            'price' => -10,
            'duration_minutes' => 0,
            'status' => 'unknown',
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'price',
            'duration_minutes',
            'status',
        ]);
});

test('patching a non-existent service returns not found', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)
        ->patchJson('/api/services/9999', [
            'price' => 75.00,
        ]);

    $response->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

test('a service can be soft deleted', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $response = $this->actingAs($manager)
        ->deleteJson("/api/services/{$service->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('services', [
        'id' => $service->id,
    ]);
});

test('a soft deleted service is not returned in the service list', function () {
    $category = ServiceCategory::factory()->create();

    $service = Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $category->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $service->delete();

    $response = $this->getJson('/api/services');

    $response
        ->assertStatus(200)
        ->assertJsonMissing([
            'id' => $service->id,
        ]);
});

test('deleting a non-existent service returns not found', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($manager)
        ->deleteJson('/api/services/9999');

    $response->assertStatus(404);
});
