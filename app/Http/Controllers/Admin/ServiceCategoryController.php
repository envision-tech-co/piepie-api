<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateServiceCategoryRequest;
use App\Http\Requests\Admin\UpdateServiceCategoryRequest;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends Controller
{
    /**
     * List all categories (admin).
     */
    public function index(): JsonResponse
    {
        $categories = ServiceCategory::orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'categories' => ServiceCategoryResource::collection($categories),
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(CreateServiceCategoryRequest $request): JsonResponse
    {
        $category = ServiceCategory::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service category created.',
            'category' => new ServiceCategoryResource($category),
        ], 201);
    }

    /**
     * Update a category.
     */
    public function update(UpdateServiceCategoryRequest $request, int $id): JsonResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $category->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Service category updated.',
            'category' => new ServiceCategoryResource($category->fresh()),
        ]);
    }

    /**
     * Toggle active/inactive.
     */
    public function toggle(int $id): JsonResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => $category->is_active ? 'Category activated.' : 'Category deactivated.',
            'category' => new ServiceCategoryResource($category->fresh()),
        ]);
    }

    /**
     * Public list of active categories (no auth).
     */
    public function publicList(): JsonResponse
    {
        $categories = ServiceCategory::active()->get();

        return response()->json([
            'success' => true,
            'categories' => ServiceCategoryResource::collection($categories),
        ]);
    }
}
