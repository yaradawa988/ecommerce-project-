<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
         return [
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:255',
            'slug'           => 'required|string|max:255|unique:products,slug',
            'sku'            => 'required|string|max:255|unique:products,sku',
            'description'    => 'nullable|string',
            'base_price'     => 'required|numeric|min:0',
            'is_weight_based'=> 'boolean',
            'is_active'      => 'boolean',

            // images
            'images'         => 'nullable|array',
            'images.*'       => 'image|max:2048',

            // variants
            'variants'              => 'nullable|array',
            'variants.*.size'       => 'nullable|string|max:255',
            'variants.*.color'      => 'nullable|string|max:255',
            'variants.*.weight'     => 'nullable|numeric',
            'variants.*.price'      => 'required_with:variants|numeric|min:0',
            'variants.*.stock'      => 'required_with:variants|integer|min:0',
        ];
    }
}