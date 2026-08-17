<?php

declare(strict_types=1);

namespace App\Http\Requests\Campaign;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateCampaignRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'The title of the campaign.',
                'example' => 'Updated Summer Sale Campaign',
            ],
            'description' => [
                'description' => 'A brief description of the campaign.',
                'example' => 'Updated description.',
            ],
            'scheduled_at' => [
                'description' => 'The scheduled time for the campaign to start.',
                'example' => '2025-12-26 10:00:00',
            ],
        ];
    }
}
