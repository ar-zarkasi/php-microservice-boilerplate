<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UpdateUser extends FormRequest
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
            'avatar' => 'nullable|file|mimes:jpeg,jpg,png|max:2048',
            'phone' => 'nullable|string|min:10|max:15',
            'roles' => 'nullable|array',
            'roles.*.id' => 'required_if:roles|string|exists:Roles,id',
        ];
    }
}
