<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
//use App\Models\ProductImage;
//use App\Models\ProductVariant;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ProductController extends Controller
{
 

public function index()
{
    $this->authorizeAbility('product.view');

    $products = Product::with('category')->paginate(10);

    // إذا لم توجد أي منتجات
    if ($products->isEmpty()) {
        return ApiResponse::success(
            [],
            'No products found',
            200
        );
    }

    return ProductResource::collection($products)
        ->additional([
            'message' => 'Products retrieved successfully'
        ]);
}


   public function store(StoreProductRequest $request)
{
    $this->authorizeAbility('product.create');

    $data = $request->validated();

    // 1) إنشاء المنتج
    $product = Product::create([
        'category_id'     => $data['category_id'],
        'name'            => $data['name'],
        'slug'            => $data['slug'],
        'sku'             => $data['sku'],
        'description'     => $data['description'] ?? null,
        'base_price'      => $data['base_price'],
        'is_weight_based' => $data['is_weight_based'] ?? false,
        'is_active'       => $data['is_active'] ?? true,
    ]);

    /* ======================================================
     *  2) رفع الصور وربطها بالمنتج
     * ====================================================== */
    if ($request->hasFile('images')) {

        foreach ($request->file('images') as $index => $img) {

            $path = $img->store('products', 'public');

            $product->images()->create([
                'path'    => $path,
                'is_main' => $index === 0,  // أول صورة رئيسية
            ]);
        }
    }

    /* ======================================================
     *  3) إضافة الـ Variants وربطها بالمنتج
     * ====================================================== */
    if (!empty($data['variants'])) {

        foreach ($data['variants'] as $variant) {

            $product->variants()->create([
                'size'   => $variant['size'] ?? null,
                'color'  => $variant['color'] ?? null,
                'weight' => $variant['weight'] ?? null,
                'price'  => $variant['price'],
                'stock'  => $variant['stock'],
            ]);
        }
    }

    /* ======================================================
     *  4) تحميل العلاقات المطلوبة للـ Resource
     * ====================================================== */
    $product->load(['category', 'images', 'variants']);

    return ApiResponse::success(
        new ProductResource($product),
        'Product created successfully',
        201
    );
}

   public function show($id)
{
    $this->authorizeAbility('product.view');

    // البحث عن المنتج
    $product = Product::with('category', 'images', 'variants')->find($id);

    // إذا المنتج غير موجود
    if (!$product) {
        return ApiResponse::error(
            'Product not found',
            404
        );
    }

    return ApiResponse::success(
        new ProductResource($product),
        'Product retrieved successfully'
    );
}


 public function update(UpdateProductRequest $request, $id)
{
    $this->authorizeAbility('product.update');

    $product = Product::with(['images', 'variants', 'category'])->find($id);

    if (!$product) {
        return ApiResponse::error('Product not found', 404);
    }

    $data = $request->validated();

    // تحديث بيانات المنتج
    $product->fill([
        'category_id'     => $data['category_id'] ?? $product->category_id,
        'name'            => $data['name'] ?? $product->name,
        'slug'            => $data['slug'] ?? $product->slug,
        'sku'             => $data['sku'] ?? $product->sku,
        'description'     => $data['description'] ?? $product->description,
        'base_price'      => $data['base_price'] ?? $product->base_price,
        'is_weight_based' => $data['is_weight_based'] ?? $product->is_weight_based,
        'is_active'       => $data['is_active'] ?? $product->is_active,
    ]);

    $product->save(); // مهم جدًا لحفظ التعديلات

    // تحديث الصور
    if (!empty($data['delete_images'])) {
        foreach ($data['delete_images'] as $imgId) {
            $img = $product->images()->find($imgId);
            if ($img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }
    }

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'is_main' => false,
            ]);
        }
    }

    // تحديث/إضافة variants
    if (!empty($data['delete_variants'])) {
        $product->variants()->whereIn('id', $data['delete_variants'])->delete();
    }

    if (!empty($data['variants'])) {
        foreach ($data['variants'] as $variant) {
            if (!empty($variant['id'])) {
                $existing = $product->variants()->find($variant['id']);
                if ($existing) {
                    $existing->update([
                        'size'   => $variant['size'] ?? null,
                        'color'  => $variant['color'] ?? null,
                        'weight' => $variant['weight'] ?? null,
                        'price'  => $variant['price'],
                        'stock'  => $variant['stock'],
                    ]);
                }
            } else {
                $product->variants()->create([
                    'size'   => $variant['size'] ?? null,
                    'color'  => $variant['color'] ?? null,
                    'weight' => $variant['weight'] ?? null,
                    'price'  => $variant['price'],
                    'stock'  => $variant['stock'],
                ]);
            }
        }
    }

    $product->load(['images', 'variants', 'category']);

    return ApiResponse::success(
        new ProductResource($product),
        'Product updated successfully'
    );
}




   public function destroy(Product $product)
{
    $this->authorizeAbility('product.delete');

    // حذف الصور من التخزين
    foreach ($product->images as $image) {
        Storage::disk('public')->delete($image->path);
    }

    // حذف الصور من DB
    $product->images()->delete();

    // حذف الـ variants
    $product->variants()->delete();

    // حذف المنتج
    $product->delete();

    return ApiResponse::success(
        null,
        'Product deleted successfully'
    );
}


     /**
     * Ability checker
     */
   private function authorizeAbility($ability)
{
    $user = auth()->user();

    abort_if(!$user || !$user->tokenCan($ability), 403, "Forbidden: Missing ability: $ability");
}

}
