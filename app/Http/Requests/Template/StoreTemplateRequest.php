<?php

declare(strict_types=1);

namespace App\Http\Requests\Template;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreTemplateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the template.',
                'example' => 'Welcome Message',
            ],
            'content' => [
                'description' => 'The content of the template.',
                'example' => 'Hello, this is a welcome message.',
            ],
        ];
    }
}
