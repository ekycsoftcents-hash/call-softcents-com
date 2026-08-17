<?php

declare(strict_types=1);

namespace App\Http\Requests\Audio;

use App\Enums\AudioType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

final class StoreAudioRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<string|Rule>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => [
                'required',
                Rule::enum(AudioType::class),
            ],
            'message' => [
                'required_if:type,tts',
                'prohibited_if:type,upload',
                'nullable',
                'string',
            ],
            'file' => [
                'required_if:type,upload',
                'prohibited_if:type,tts',
                'nullable',
                File::types(['mp3', 'wav', 'ogg', 'm4a'])
                    ->max('10mb'),
            ],
            'tts_artist_id' => [
                'required_if:type,tts',
                'prohibited_if:type,upload',
                'nullable',
                'exists:tts_artists,id',
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'tts_artist_id' => [
                'description' => 'The ID of the TTS artist (required if type is tts).',
                'example' => '1',
            ],
            'description' => [
                'description' => 'The description of the audio.',
                'example' => 'Greeting for new customers.',
            ],
            'file' => [
                'description' => 'The audio file to upload (mp3, wav, ogg, m4a). Required if type is upload.',
            ],

            'message' => [
                'description' => 'The message for TTS generation (required if type is tts).',
                'example' => 'Hello, welcome to our service.',
            ],
            'title' => [
                'description' => 'The title of the audio.',
                'example' => 'Welcome Message',
            ],
            'type' => [
                'description' => 'The type of audio (tts or upload).',
                'example' => 'tts|upload',
            ],
        ];
    }
}
