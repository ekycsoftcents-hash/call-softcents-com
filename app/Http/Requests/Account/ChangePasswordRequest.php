<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class ChangePasswordRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'current_password' => [
                'description' => 'Current password',
                'example' => 'current_password',
            ],
            'password' => [
                'description' => 'New password',
                'example' => 'password',
            ],
            'password_confirmation' => [
                'description' => 'Confirmation of the new password',
                'example' => 'password',
            ],
        ];
    }
}
