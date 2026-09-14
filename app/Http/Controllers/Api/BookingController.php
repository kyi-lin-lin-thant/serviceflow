<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $bookings = Booking::all();

        return response()->json($bookings);
    }
}
