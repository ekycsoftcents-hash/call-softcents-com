<?php

declare(strict_types=1);

namespace App\Http\Requests\Audio;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateAudioRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'The title of the audio. (Optional)',
                'example' => 'Welcome Message Updated',
            ],
            'description' => [
                'description' => 'The description of the audio. (Optional)',
                'example' => 'Updated greeting.',
            ],
        ];
    }
}
