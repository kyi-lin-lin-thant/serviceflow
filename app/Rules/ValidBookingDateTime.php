<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidBookingDateTime implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = request()->input('booking_date');
        $time = request()->input('booking_time');

        if (! is_string($date) || ! is_string($time)) {
            return;
        }

        $bookingDateTime = now()->createFromFormat(
            'Y-m-d H:i',
            "{$date} {$time}"
        );

        if ($bookingDateTime === null) {
            return;
        }

        if ($bookingDateTime->isPast()) {
            $fail('The booking date and time must be in the future.');
        }
    }
}
