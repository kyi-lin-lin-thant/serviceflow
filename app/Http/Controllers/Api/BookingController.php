<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $user = request()->user();

        Gate::authorize('viewAny', Booking::class);

        $bookings = $user->role === UserRole::MANAGER
            ? Booking::all()
            : Booking::where('customer_id', $user->id)->get();

        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'service_id' => $request->validated('service_id'),
            'booking_date' => $request->validated('booking_date'),
            'booking_time' => $request->validated('booking_time'),
            'address' => $request->validated('address'),
            'notes' => $request->validated('notes'),
            'status' => BookingStatus::PENDING,
        ]);

        return response()->json($booking, 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        Gate::authorize('view', $booking);

        return response()->json($booking);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('update', $booking);
        $booking->update($request->validated());

        return response()->json($booking->fresh());
    }
}
