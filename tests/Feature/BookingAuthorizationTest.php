<?php

use App\Models\Booking;
use App\Models\User;

test('customer can view their own booking', function () {
    $customer = User::factory()->create();
    $booking = Booking::factory()->create([
        'customer_id' => $customer->id,
    ]);

    $response = $this->actingAs($customer)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertOk();
});

test('customer cannot view another customers booking', function () {
    $customer = User::factory()->create();
    $anotherCustomer = User::factory()->create();

    $booking = Booking::factory()->create([
        'customer_id' => $anotherCustomer->id,
    ]);

    $response = $this->actingAs($customer)
        ->getJson("/api/bookings/{$booking->id}");

    $response->assertForbidden();
});
