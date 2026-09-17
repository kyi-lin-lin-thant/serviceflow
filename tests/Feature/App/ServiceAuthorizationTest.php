<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;

function serviceData(int $categoryId): array
{
    return [
        'name' => 'House Cleaning',
        'description' => 'Professional cleaning service',
        'category_id' => $categoryId,
        'price' => 50.00,
        'duration_minutes' => 120,
        'status' => 'active',
    ];
}

/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/

// Staff cannot create
test('staff cannot create a service', function () {
    $staff = User::factory()->staff()->create();
    $category = ServiceCategory::factory()->create();

    $response = $this->actingAs($staff)
        ->postJson('/api/services', serviceData($category->id));

    $response->assertForbidden();
});

// Customer cannot create
test('customer cannot create a service', function () {
    $customer = User::factory()->customer()->create();
    $category = ServiceCategory::factory()->create();

    $response = $this->actingAs($customer)
        ->postJson('/api/services', serviceData($category->id));

    $response->assertForbidden();
});

// Manager can create
test('manager can create a service', function () {
    $manager = User::factory()->manager()->create();
    $category = ServiceCategory::factory()->create();

    $response = $this->actingAs($manager)
        ->postJson('/api/services', serviceData($category->id));

    $response->assertSuccessful();

    $this->assertDatabaseHas('services', [
        'name' => 'House Cleaning',
        'category_id' => $category->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/

// Staff cannot update
test('staff cannot update a service', function () {
    $staff = User::factory()->staff()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($staff)
        ->putJson("/api/services/{$service->id}", serviceData($service->category_id));

    $response->assertForbidden();
});

// Customer cannot update
test('customer cannot update a service', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->putJson("/api/services/{$service->id}", serviceData($service->category_id));

    $response->assertForbidden();
});

// Manager can update
test('manager can update a service', function () {
    $manager = User::factory()->manager()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($manager)
        ->putJson("/api/services/{$service->id}", [
            'name' => 'Updated Cleaning Service',
            'description' => 'Updated description',
            'category_id' => $service->category_id,
            'price' => 75.00,
            'duration_minutes' => 150,
            'status' => 'inactive',
        ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('services', [
        'name' => 'Updated Cleaning Service',
        'price' => 75.00,
    ]);
});

/*
|--------------------------------------------------------------------------
| PATCH
|--------------------------------------------------------------------------
*/

// Staff cannot patch
test('staff cannot patch a service', function () {
    $staff = User::factory()->staff()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($staff)
        ->patchJson("/api/services/{$service->id}", [
            'name' => 'Updated Service',
        ]);

    $response->assertForbidden();
});

// Customer cannot patch
test('customer cannot patch a service', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->patchJson("/api/services/{$service->id}", [
            'name' => 'Updated Service',
        ]);

    $response->assertForbidden();
});

// Manager can patch
test('manager can patch a service', function () {
    $manager = User::factory()->manager()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($manager)
        ->patchJson("/api/services/{$service->id}", [
            'name' => 'Patched Service',
        ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Patched Service',
    ]);
});

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

// Staff cannot delete
test('staff cannot delete a service', function () {
    $staff = User::factory()->staff()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($staff)
        ->deleteJson("/api/services/{$service->id}");

    $response->assertForbidden();
});

// Customer cannot delete
test('customer cannot delete a service', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->deleteJson("/api/services/{$service->id}");

    $response->assertForbidden();
});

// Manager can delete
test('manager can delete a service', function () {
    $manager = User::factory()->manager()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($manager)
        ->deleteJson("/api/services/{$service->id}");

    $response->assertSuccessful();

    $this->assertSoftDeleted('services', [
        'id' => $service->id,
    ]);
});
