<?php

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

// Create User from Factories
test('a user factory creates a factory by default', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(UserRole::CUSTOMER);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => UserRole::CUSTOMER->value,
    ]);
});

test('user factory can create staff and manager roles', function () {
    $staff = User::factory()->staff()->create();
    $manager = User::factory()->manager()->create();

    expect($staff->role)->toBe(UserRole::STAFF)
        ->and($manager->role)->toBe(UserRole::MANAGER);
});

test('user role is cast to UserRole enum', function () {
    $user = User::factory()->staff()->create();

    expect($user->role)->toBeInstanceOf(UserRole::class)
        ->and($user->role)->toBe(UserRole::STAFF);
});

test('user factory hashes the password', function () {
    $user = User::factory()->create();

    expect($user->password)->not->toBe('password')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

// Users | Bookings
// test('a user can have many bookings', function () {
//     $user = User::factory()->create();
//     $service = Service::factory()->create();

//     $booking1 = Booking::factory()->create([
//         'customer_id' => $user->id,
//         'service_id' => $service->id,
//     ]);

//     $booking2 = Booking::factory()->create([
//         'customer_id' => $user->id,
//         'service_id' => $service->id,
//     ]);

//     expect($user->bookings)
//         ->toHaveCount(2)
//         ->and($user->bookings->pluck('id')->toArray())
//         ->toContain($booking1->id, $booking2->id);
// });

test('user has a bookings relationship', function () {
    $user = User::factory()->create();

    expect($user->bookings())
        ->toBeInstanceOf(HasMany::class);
});

// Users | Staffs & Job Assignments
test('user has staff relationships', function () {
    $user = User::factory()->staff()->create();

    expect($user->jobAssignments())
        ->toBeInstanceOf(HasMany::class)
        ->and($user->staffAvailabilities())
        ->toBeInstanceOf(HasMany::class);
});
