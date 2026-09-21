<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/
test('customer can create a booking', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->postJson('/api/bookings', [
            'service_id' => $service->id,
            'booking_date' => '2026-10-01',
            'booking_time' => '10:00',
            'address' => '123 High Street, London',
            'notes' => 'Please call before arrival.',
        ]);

    $response->assertCreated();

    $response->assertJson([
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $this->assertDatabaseHas('bookings', [
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);
});

test('guest cannot create a booking', function () {
    $service = Service::factory()->create();

    $response = $this->postJson('/api/bookings', [
        'service_id' => $service->id,
        'booking_date' => '2026-10-01',
        'booking_time' => '10:00',
        'address' => '123 High Street, London',
    ]);

    $response->assertUnauthorized();
});

test('staff cannot create a booking', function () {
    $staff = User::factory()->staff()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($staff)
        ->postJson('/api/bookings', [
            'service_id' => $service->id,
            'booking_date' => '2026-10-01',
            'booking_time' => '10:00',
            'address' => '123 High Street, London',
        ]);

    $response->assertForbidden();
});

test('manager cannot create a booking', function () {
    $manager = User::factory()->manager()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($manager)
        ->postJson('/api/bookings', [
            'service_id' => $service->id,
            'booking_date' => '2026-10-01',
            'booking_time' => '10:00',
            'address' => '123 High Street, London',
        ]);

    $response->assertForbidden();
});

test('customer cannot create a booking for another customer', function () {
    $customer = User::factory()->customer()->create();
    $anotherCustomer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->postJson('/api/bookings', [
            'customer_id' => $anotherCustomer->id,
            'service_id' => $service->id,
            'booking_date' => '2026-10-01',
            'booking_time' => '10:00',
            'address' => '123 High Street, London',
        ]);

    $response->assertCreated();

    $this->assertDatabaseHas('bookings', [
        'customer_id' => $customer->id,
        'service_id' => $service->id,
    ]);

    $this->assertDatabaseMissing('bookings', [
        'customer_id' => $anotherCustomer->id,
        'service_id' => $service->id,
    ]);
});

test('booking creation requires valid booking data', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)
        ->postJson('/api/bookings', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'service_id',
            'booking_date',
            'booking_time',
            'address',
        ]);
});

test('booking creation rejects a non-existent service', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)
        ->postJson('/api/bookings', [
            'service_id' => 999999,
            'booking_date' => '2026-10-01',
            'booking_time' => '10:00',
            'address' => '123 High Street, London',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['service_id']);
});

/*
|--------------------------------------------------------------------------
| READ
|--------------------------------------------------------------------------
*/
test('customer can list their own bookings only', function () {
    $customer = User::factory()->customer()->create();
    $anotherCustomer = User::factory()->customer()->create();

    $ownBooking = Booking::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $otherBooking = Booking::factory()->create([
        'customer_id' => $anotherCustomer->id,
    ]);

    $response = $this->actingAs($customer)
        ->getJson('/api/bookings');

    $response->assertSuccessful();

    $response->assertJsonCount(1);

    $response->assertJsonFragment([
        'id' => $ownBooking->id,
    ]);

    $response->assertJsonMissing([
        'id' => $otherBooking->id,
    ]);
});

test('manager can list all bookings', function () {
    $manager = User::factory()->manager()->create();

    $customerA = User::factory()->customer()->create();
    $customerB = User::factory()->customer()->create();

    $bookingA = Booking::factory()->create([
        'customer_id' => $customerA->id,
    ]);

    $bookingB = Booking::factory()->create([
        'customer_id' => $customerB->id,
    ]);

    $response = $this->actingAs($manager)
        ->getJson('/api/bookings');

    $response->assertSuccessful();

    $response->assertJsonCount(2);

    $response->assertJsonFragment([
        'id' => $bookingA->id,
    ]);

    $response->assertJsonFragment([
        'id' => $bookingB->id,
    ]);
});

test('staff cannot list bookings', function () {
    $staff = User::factory()->staff()->create();

    Booking::factory()->count(2)->create();

    $response = $this->actingAs($staff)
        ->getJson('/api/bookings');

    $response->assertForbidden();
});

test('guest cannot list bookings', function () {
    $response = $this->getJson('/api/bookings');

    $response->assertUnauthorized();
});

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/
test('customer can update their own booking', function () {
    $customer = User::factory()->customer()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'service_id' => $service->id,
            'booking_date' => '2026-11-15',
            'booking_time' => '14:00',
            'address' => '456 New Street, London',
            'notes' => 'Updated notes.',
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'id' => $booking->id,
            'service_id' => $service->id,
            'address' => '456 New Street, London',
            'notes' => 'Updated notes.',
        ]);
});

test('customer cannot update another customers booking', function () {
    $customer = User::factory()->customer()->create();
    $anotherCustomer = User::factory()->customer()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $anotherCustomer->id,
    ]);

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'address' => '456 New Street, London',
        ]);

    $response->assertForbidden();
});

test('manager can update any booking', function () {
    $manager = User::factory()->manager()->create();

    $booking = Booking::factory()->create();

    $response = $this->actingAs($manager)
        ->putJson("/api/bookings/{$booking->id}", [
            'address' => '456 Manager Street, London',
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'id' => $booking->id,
            'address' => '456 Manager Street, London',
        ]);
});

test('staff cannot update a booking', function () {
    $staff = User::factory()->staff()->create();

    $booking = Booking::factory()->create();

    $response = $this->actingAs($staff)
        ->putJson("/api/bookings/{$booking->id}", [
            'address' => '456 Staff Street, London',
        ]);

    $response->assertForbidden();
});

test('guest cannot update a booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->putJson("/api/bookings/{$booking->id}", [
        'address' => '456 Guest Street, London',
    ]);

    $response->assertUnauthorized();
});

test('customer cannot change booking ownership or status', function () {
    $customer = User::factory()->customer()->create();
    $anotherCustomer = User::factory()->customer()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'customer_id' => $anotherCustomer->id,
            'status' => 'confirmed',
            'address' => '456 Secure Street, London',
        ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'customer_id' => $customer->id,
        'status' => 'pending',
        'address' => '456 Secure Street, London',
    ]);
});

test('booking update validates supplied fields', function () {
    $customer = User::factory()->customer()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'service_id' => 999999,
            'booking_time' => 'invalid-time',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([
            'service_id',
            'booking_time',
        ]);
});

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| DATE TIME ISSUES
|--------------------------------------------------------------------------
*/
test('booking date cannot be in the past', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($customer)
        ->postJson('/api/bookings', [
            'service_id' => $service->id,
            'booking_date' => now()->subDay()->toDateString(),
            'booking_time' => '10:00',
            'address' => '123 High Street, London',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['booking_date']);
});

test('booking cannot use a past time today', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $pastTime = now()->subHour()->format('H:i');

    $response = $this->actingAs($customer)
        ->postJson('/api/bookings', [
            'service_id' => $service->id,
            'booking_date' => now()->toDateString(),
            'booking_time' => $pastTime,
            'address' => '123 High Street, London',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['booking_time']);
});

test('customer can update booking date without changing time', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'booking_date' => now()->addDays(2)->toDateString(),
        'booking_time' => '14:00',
    ]);

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'booking_date' => now()->addDays(3)->toDateString(),
        ]);

    $response->assertOk()
        ->assertJsonPath(
            'booking_date',
            now()->addDays(3)->startOfDay()->toISOString()
        );
});

test('customer can update booking time without changing date', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'booking_date' => now()->addDays(2)->toDateString(),
        'booking_time' => '14:00',
    ]);

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'booking_time' => '16:00',
        ]);

    $response->assertOk();
});

test('booking update cannot move booking into the past', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'booking_date' => now()->addDays(2)->toDateString(),
        'booking_time' => '14:00',
    ]);

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'booking_date' => now()->subDay()->toDateString(),
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['booking_date']);
});

test('booking update cannot use a past time today', function () {
    $customer = User::factory()->customer()->create();
    $service = Service::factory()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
        'service_id' => $service->id,
        'booking_date' => now()->toDateString(),
        'booking_time' => '23:00',
    ]);

    $pastTime = now()->subHour()->format('H:i');

    $response = $this->actingAs($customer)
        ->putJson("/api/bookings/{$booking->id}", [
            'booking_time' => $pastTime,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['booking_time']);
});
