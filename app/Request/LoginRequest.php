<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class LoginRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
            'remember_me' => 'nullable|boolean',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'The username field is required.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
