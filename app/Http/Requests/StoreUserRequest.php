<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
class StoreUserRequest extends FormRequest
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
            'name' => ['required','string','max:255','regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required','email:rfc,dns','max:255','unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers()->symbols()->mixedCase()],
            'profile_image' => ['nullable','image','mimes:png,jpg,jpeg','max:2048'],
            'roles' => ['required','array'],
            'roles.*' => ['exists:roles,id'],
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