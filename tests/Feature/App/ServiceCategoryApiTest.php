<?php

use App\Models\Service;
use App\Models\ServiceCategory;

// Create | Success
test('a valid service category can be created', function () {
    $response = $this->postJson('/api/service-categories', [
        'name' => 'Cleaning',
        'description' => 'Professional cleaning services',
        'status' => 'active',
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Cleaning');

    $this->assertDatabaseHas('service_categories', [
        'name' => 'Cleaning',
        'status' => 'active',
    ]);
});

// Create | Missing required fields
test('service category creation fails when required fields are missing', function () {
    $response = $this->postJson('/api/service-categories', [
        'description' => 'Professional cleaning services',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'status',
        ]);
});

// Create | Duplicate name
test('service category creation fails with a duplicate name', function () {
    ServiceCategory::factory()->create([
        'name' => 'Cleaning',
    ]);

    $response = $this->postJson('/api/service-categories', [
        'name' => 'Cleaning',
        'description' => 'Another cleaning category',
        'status' => 'active',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
        ]);
});

// List | Success
test('service categories can be listed', function () {
    ServiceCategory::factory()->count(3)->create();

    $response = $this->getJson('/api/service-categories');

    $response
        ->assertStatus(200)
        ->assertJsonCount(3);
});

// Show | Success
test('an existing service category can be viewed', function () {
    $serviceCategory = ServiceCategory::factory()->create([
        'name' => 'Cleaning',
    ]);

    $response = $this->getJson(
        "/api/service-categories/{$serviceCategory->id}"
    );

    $response
        ->assertStatus(200)
        ->assertJsonPath('id', $serviceCategory->id)
        ->assertJsonPath('name', 'Cleaning');
});

// Show | Not found
test('viewing a non-existent service category returns not found', function () {
    $response = $this->getJson('/api/service-categories/9999');

    $response->assertStatus(404);
});

// Update | Success
test('a valid service category can be updated', function () {
    $serviceCategory = ServiceCategory::factory()->create([
        'name' => 'Cleaning',
        'description' => 'Professional cleaning services',
        'status' => 'active',
    ]);

    $response = $this->putJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'name' => 'Premium Cleaning',
            'description' => 'Premium professional cleaning services',
            'status' => 'inactive',
        ]
    );

    $response
        ->assertStatus(200)
        ->assertJsonPath('message', 'Service Category updated successfully.')
        ->assertJsonPath('data.name', 'Premium Cleaning')
        ->assertJsonPath('data.status', 'inactive');

    $this->assertDatabaseHas('service_categories', [
        'id' => $serviceCategory->id,
        'name' => 'Premium Cleaning',
        'status' => 'inactive',
    ]);
});

// Update | Same name is allowed
test('a service category can keep its existing name when updated', function () {
    $serviceCategory = ServiceCategory::factory()->create([
        'name' => 'Cleaning',
    ]);

    $response = $this->putJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'name' => 'Cleaning',
            'description' => 'Updated description',
            'status' => 'active',
        ]
    );

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Cleaning');
});

// Update | Duplicate name
test('service category update fails with a duplicate name', function () {
    $existingCategory = ServiceCategory::factory()->create([
        'name' => 'Cleaning',
    ]);

    $serviceCategory = ServiceCategory::factory()->create([
        'name' => 'Maintenance',
    ]);

    $response = $this->putJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'name' => $existingCategory->name,
            'description' => 'Updated description',
            'status' => 'active',
        ]
    );

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
        ]);
});

// Update | Missing required fields
test('service category update fails when required fields are missing', function () {
    $serviceCategory = ServiceCategory::factory()->create();

    $response = $this->putJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'description' => 'Updated description',
        ]
    );

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'status',
        ]);
});

// Update | Invalid data
test('service category update fails with invalid data', function () {
    $serviceCategory = ServiceCategory::factory()->create();

    $response = $this->putJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'name' => '',
            'description' => 123,
            'status' => 'unknown',
        ]
    );

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'description',
            'status',
        ]);
});

// Update | Not found
test('updating a non-existent service category returns not found', function () {
    $response = $this->putJson('/api/service-categories/9999', [
        'name' => 'Updated Category',
        'description' => 'Updated description',
        'status' => 'active',
    ]);

    $response->assertStatus(404);
});

// Patch | Partial update
test('a service category can be partially updated', function () {
    $serviceCategory = ServiceCategory::factory()->create([
        'name' => 'Cleaning',
        'description' => 'Professional cleaning services',
        'status' => 'active',
    ]);

    $response = $this->patchJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'status' => 'inactive',
        ]
    );

    $response
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Cleaning')
        ->assertJsonPath('data.description', 'Professional cleaning services')
        ->assertJsonPath('data.status', 'inactive');

    $this->assertDatabaseHas('service_categories', [
        'id' => $serviceCategory->id,
        'name' => 'Cleaning',
        'status' => 'inactive',
    ]);
});

// Patch | Duplicate name
test('service category patch fails with a duplicate name', function () {
    $existingCategory = ServiceCategory::factory()->create([
        'name' => 'Cleaning',
    ]);

    $serviceCategory = ServiceCategory::factory()->create([
        'name' => 'Maintenance',
    ]);

    $response = $this->patchJson(
        "/api/service-categories/{$serviceCategory->id}",
        [
            'name' => $existingCategory->name,
        ]
    );

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
        ]);
});

// Patch | Not found
test('patching a non-existent service category returns not found', function () {
    $response = $this->patchJson('/api/service-categories/9999', [
        'status' => 'inactive',
    ]);

    $response->assertStatus(404);
});

// Delete | Soft delete
test('a service category can be soft deleted', function () {
    $serviceCategory = ServiceCategory::factory()->create();

    $response = $this->deleteJson(
        "/api/service-categories/{$serviceCategory->id}"
    );

    $response->assertNoContent();

    $this->assertSoftDeleted('service_categories', [
        'id' => $serviceCategory->id,
    ]);
});

// Delete | Not returned after soft delete
test('a soft deleted service category is not returned in the category list', function () {
    $serviceCategory = ServiceCategory::factory()->create();

    $serviceCategory->delete();

    $response = $this->getJson('/api/service-categories');

    $response
        ->assertStatus(200)
        ->assertJsonMissing([
            'id' => $serviceCategory->id,
        ]);
});

// Delete | Category with services
test('a service category with services cannot be deleted', function () {
    $serviceCategory = ServiceCategory::factory()->create();

    Service::create([
        'name' => 'House Cleaning',
        'description' => 'Professional house cleaning service',
        'category_id' => $serviceCategory->id,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ]);

    $response = $this->deleteJson(
        "/api/service-categories/{$serviceCategory->id}"
    );

    $response->assertStatus(409);

    $this->assertDatabaseHas('service_categories', [
        'id' => $serviceCategory->id,
        'deleted_at' => null,
    ]);
});

// Delete | Not found
test('deleting a non-existent service category returns not found', function () {
    $response = $this->deleteJson('/api/service-categories/9999');

    $response->assertStatus(404);
});
