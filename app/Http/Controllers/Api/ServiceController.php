<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Requests\PatchServiceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::all();

        return response()->json($services);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return response()->json([
            'message' => 'Service created successfully.',
            'data' => $service,
        ], 201);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json($service);
    } 

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return response()->json([
            'message' => 'Service updated successfully.',
            'data' => $service->fresh(),
        ]);
    }

    public function patch(PatchServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return response()->json([
            'message' => 'Service updated successfully.',
            'data' => $service->fresh(),
        ]);
    }

    public function destroy(Service $service): Response
    {
        $service->delete();

        return response()->noContent();
    }
}
