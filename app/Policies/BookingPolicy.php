<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            UserRole::CUSTOMER,
            UserRole::MANAGER,
        ], true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->role === UserRole::MANAGER
            || (
                $user->role === UserRole::CUSTOMER
                && $user->id === $booking->customer_id
            );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::CUSTOMER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->role === UserRole::MANAGER
            || (
                $user->role === UserRole::CUSTOMER
                && $user->id === $booking->customer_id
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->role === UserRole::MANAGER
            || (
                $user->role === UserRole::CUSTOMER
                && $user->id === $booking->customer_id
            );
    }
}
