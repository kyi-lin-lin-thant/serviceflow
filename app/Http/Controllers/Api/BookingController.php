<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $bookings = Booking::all();

        return response()->json($bookings);
    }

    public function show(Booking $booking): JsonResponse
    {
        Gate::authorize('view', $booking);

        return response()->json($booking);
    }
}
