<?php

declare(strict_types=1);

namespace App\Http\Requests\Contact;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;

final class UpdateContactRequest extends FormRequest
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
            'phone_number' => [
                'sometimes',
                'string',
                'phone:BD',
                Rule::unique('contacts', 'phone_number')
                    ->where('group_id', $this->route('contact')?->group_id)
                    ->ignore($this->route('contact')?->id),
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'phone_number' => [
                'description' => 'The phone number of the contact. (Optional)',
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
