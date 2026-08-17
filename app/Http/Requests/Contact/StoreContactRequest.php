<?php

declare(strict_types=1);

namespace App\Http\Requests\Contact;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;

final class StoreContactRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<string>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'phone_number' => [
                'required',
                'string',
                'phone:BD',
                Rule::unique('contacts', 'phone_number')
                    ->where('group_id', $this->input('group_id')),
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'group_id' => [
                'description' => 'The ID of the group.',
            ],
            'phone_number' => [
                'description' => 'The phone number of the contact.',
                'example' => '+8801XXXXXXXXX',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone_number' => rescue(fn () => new PhoneNumber($this->input('phone_number'), 'BD')->formatE164()),
        ]);
    }
}
