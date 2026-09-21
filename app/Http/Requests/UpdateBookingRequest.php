<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Rules\ValidBookingUpdateDateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['sometimes', 'integer', 'exists:services,id'],
            'booking_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'booking_time' => ['sometimes', 'date_format:H:i'],
            'address' => ['sometimes', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            // If normal validation already failed,
            // do not run the custom date/time rule.
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            // If neither date nor time is being updated,
            // there is nothing to check.
            if (! $this->has('booking_date') && ! $this->has('booking_time')) {
                return;
            }

            $booking = $this->route('booking');

            if (! $booking instanceof Booking) {
                return;
            }

            $rule = new ValidBookingUpdateDateTime($booking);

            $message = $rule->getErrorMessage();

            if ($message !== null) {
                $field = $this->has('booking_date')
                    ? 'booking_date'
                    : 'booking_time';

                $validator->errors()->add($field, $message);
            }
        });
    }
}
