<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return  true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      $userId = $this->route('id');

        return [
             'name' => ['nullable','string','max:255','regex:/^[a-zA-Z\s]+$/'],
            'email' => ['nullable','email:rfc,dns','max:255',Rule::unique('users', 'email')->ignore($this->id)],
            'password' => ['nullable', Password::min(8)->letters()->numbers()->symbols()->mixedCase()],
            'profile_image' => ['nullable','image','mimes:png,jpg,jpeg','max:2048'],
            'roles'         => ['nullable','array'],
            'roles.*'       => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'The email format is invalid.',
            'email.unique' => 'This email is already taken.',
            'password.*' => 'Password must be at least 8 characters, include letters, numbers, symbols, and mixed case.',
            'roles.required' => 'At least one role must be selected.',
        ];
    }
}