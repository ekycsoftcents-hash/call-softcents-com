<?php

declare(strict_types=1);

namespace App\Http\Requests\Otp;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreOtpRequest extends FormRequest
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
            'caller_id' => [
                'required',
                'exists:callers,id,enabled,1',
                // The caller must belong to the authenticated user by checking whereHas - because its belongsToMany relationship,
            ],
            'code' => ['required', 'integer', 'max_digits:10'],
            'recipient' => ['required', 'string', 'max:15'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'caller_id' => [
                'description' => 'The ID of the caller to be used for sending the OTP.',
                'example' => 1,
            ],
            'code' => [
                'description' => 'The OTP code to be sent to the recipient.',
                'example' => 123456,
            ],
            'recipient' => [
                'description' => 'The phone number of the recipient who will receive the OTP.',
                'example' => '+1234567890',
            ],
        ];
    }
}
