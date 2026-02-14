<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
{
    // نحاول الحصول على المنتج من الـ route
    $product = $this->route('product'); 

    // إذا كان object (route model binding) استخدم id
    $productId = is_object($product) ? $product->id : $product;

    return [
        'category_id'    => 'nullable|exists:categories,id',
        'name'           => 'nullable|string|max:255',
        'slug'           => "nullable|string|max:255|unique:products,slug,$productId",
        'sku'            => "nullable|string|max:255|unique:products,sku,$productId",
        'description'    => 'nullable|string',
        'base_price'     => 'nullable|numeric|min:0',
        'is_weight_based'=> 'boolean',
        'is_active'      => 'boolean',

        // الصور
        'images'         => 'nullable|array',
        'images.*'       => 'image|max:2048',

        // IDs للصور التي سيتم حذفها
        'delete_images'  => 'nullable|array',
        'delete_images.*'=> 'exists:product_images,id',

        // تحديث/إضافة variants
        'variants'                       => 'nullable|array',
        'variants.*.id'                  => 'nullable|exists:product_variants,id',
        'variants.*.size'                => 'nullable|string|max:255',
        'variants.*.color'               => 'nullable|string|max:255',
        'variants.*.weight'              => 'nullable|numeric',
        'variants.*.price'               => 'required_with:variants|numeric|min:0',
        'variants.*.stock'               => 'required_with:variants|integer|min:0',

        // IDs للـ variants التي سيتم حذفها
        'delete_variants'   => 'nullable|array',
        'delete_variants.*' => 'exists:product_variants,id',
    ];
}
}