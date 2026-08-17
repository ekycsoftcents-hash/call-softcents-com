<?php

declare(strict_types=1);

namespace App\Http\Requests\Group;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateGroupRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the group. (Optional)',
            ],
            'description' => [
                'description' => 'The description of the group. (Optional)',
            ],
        ];
    }
}
