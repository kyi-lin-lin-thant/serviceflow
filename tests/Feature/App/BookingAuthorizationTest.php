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
