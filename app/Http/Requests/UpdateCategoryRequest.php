<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return $this->user()->tokenCan('category.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:categories,id',
            'name'      => 'sometimes|string|max:255',
            'slug'      => 'sometimes|string|max:255|unique:categories,slug,' . $this->category->id,
            'description' => 'nullable|string',
            'is_active'  => 'boolean'
        ];
    }
}
