<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Helpers\ApiResponse;

class CategoryController extends Controller
{
    
    

    /**
     * List all categories
     */
    public function index()
    {
        $this->authorizeAbility('category.view');

        $categories = Category::with('children')->get();

        return ApiResponse::success(
            CategoryResource::collection($categories),
            $categories->isEmpty() ? 'No categories found' : 'Categories fetched successfully'
        );
    }

    /**
     * Create category
     */
    public function store(StoreCategoryRequest $request)
    {
        $this->authorizeAbility('category.create');

        $category = Category::create($request->validated());

        return ApiResponse::success(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    /**
     * Show category
     */
    public function show(Category $category)
    {
        $this->authorizeAbility('category.view');

        return ApiResponse::success(
            new CategoryResource($category->load('children')),
            'Category retrieved successfully'
        );
    }

    /**
     * Update category
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorizeAbility('category.update');

        $category->update($request->validated());

        return ApiResponse::success(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    /**
     * Delete category
     */
   public function destroy($id)
{
    $this->authorizeAbility('category.delete');

    $category = Category::find($id);

    if (!$category) {
        return ApiResponse::error(' This Category does not exist', 404);
    }

    $category->delete();

    return ApiResponse::success(
        null,
        'Category deleted successfully'
    );
}


    /**
     * Ability checker
     */
    private function authorizeAbility($ability)
    {
        $user = auth()->user();

        if (!$user || !$user->tokenCan($ability)) {
            return ApiResponse::error("Forbidden: Missing ability: $ability", 403);
        }
    }
}