<?php

namespace App\Rules;

use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBookingUpdateDateTime implements ValidationRule
{
    public function __construct(
        private Booking $booking
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = $this->getErrorMessage();

        if ($message !== null) {
            $fail($message);
        }
    }

    public function getErrorMessage(): ?string
    {
        $date = request()->input('booking_date');

        if ($date === null) {
            $date = $this->booking->booking_date->format('Y-m-d');
        }

        $time = request()->input('booking_time');

        if ($time === null) {
            $time = $this->booking->booking_time->format('H:i');
        }

        if (! is_string($date) || ! is_string($time)) {
            return null;
        }

        $bookingDateTime = now()->createFromFormat(
            'Y-m-d H:i',
            "{$date} {$time}"
        );

        if ($bookingDateTime === null) {
            return null;
        }

        if ($bookingDateTime->isPast()) {
            return 'The booking date and time must be in the future.';
        }

        return null;
    }
}
