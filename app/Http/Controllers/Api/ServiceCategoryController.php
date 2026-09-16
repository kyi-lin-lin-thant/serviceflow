<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatchServiceCategoryRequest;
use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $serviceCategories = ServiceCategory::all();

        return response()->json($serviceCategories);
    }

    public function store(StoreServiceCategoryRequest $request): JsonResponse
    {
        $serviceCategory = ServiceCategory::create($request->validated());

        return response()->json([
            'message' => 'Service Category created successfully.',
            'data' => $serviceCategory,
        ], 201);
    }

    public function show(ServiceCategory $serviceCategory): JsonResponse
    {
        return response()->json($serviceCategory);
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory): JsonResponse
    {
        $serviceCategory->update($request->validated());

        return response()->json([
            'message' => 'Service Category updated successfully.',
            'data' => $serviceCategory->fresh(),
        ]);
    }

    public function patch(PatchServiceCategoryRequest $request, ServiceCategory $serviceCategory): JsonResponse
    {
        $serviceCategory->update($request->validated());

        return response()->json([
            'message' => 'Service Category updated successfully.',
            'data' => $serviceCategory->fresh(),
        ]);
    }

    public function destroy(ServiceCategory $serviceCategory): JsonResponse
    {
        if ($serviceCategory->services()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a service category that has services.',
            ], 409);
        }

        $serviceCategory->delete();

        return response()->json(null, 204);
    }
}
