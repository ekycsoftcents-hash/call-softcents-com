<?php

declare(strict_types=1);

namespace App\Http\Requests\Account;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;

final class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()?->id),
            ],
            'phone' => [
                'nullable',
                'phone:BD',
                Rule::unique('users')->ignore($this->user()?->id),
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the user.',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'The email of the user. If changed, the email verification will be reset.',
                'example' => 'johndoe@example.com',
            ],
            'phone' => [
                'description' => 'The phone number of the user.',
                'example' => '+8801XXXXXXXXX',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => rescue(fn () => (new PhoneNumber($this->input('phone'), 'BD'))->formatE164()),
        ]);
    }
}
