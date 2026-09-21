<?php

use App\Models\Booking;
use App\Models\User;

// View own booking
test('customer can view their own booking', function () {
    $customer = User::factory()->customer()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this->actingAs($customer)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertSuccessful();

    $response->assertJson([
        'id' => $booking->id,
    ]);
});

// View another customer's booking
test('customer cannot view another customers booking', function () {
    $customer = User::factory()->customer()->create();

    $anotherCustomer = User::factory()->customer()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $anotherCustomer->id,
    ]);

    $response = $this->actingAs($customer)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertForbidden();
});

test('manager can view any booking', function () {
    $manager = User::factory()->manager()->create();

    $booking = Booking::factory()->create();

    $response = $this->actingAs($manager)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertSuccessful()
        ->assertJson([
            'id' => $booking->id,
        ]);
});

test('staff cannot view a booking', function () {
    $staff = User::factory()->staff()->create();

    $booking = Booking::factory()->create();

    $response = $this->actingAs($staff)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertForbidden();
});

test('guest cannot view a booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->getJson("/api/bookings/{$booking->id}");

    $response->assertUnauthorized();
});
