<?php

declare(strict_types=1);

namespace App\Http\Requests\Call;

use App\Enums\AudioApproval;
use App\Enums\AudioConversionStatus;
use App\Rules\EnsureUserHasSufficientBalanceForCall;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;

final class StoreCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caller_id' => [
                'required',
                Rule::exists('callers', 'id')
                    ->where('enabled', true),
                Rule::exists('caller_user', 'caller_id')
                    ->where('user_id', $this->user()?->id),
            ],
            'audio_id' => [
                'required',
                Rule::exists('audio', 'id')
                    ->where('user_id', $this->user()?->id)
                    ->where('approval', AudioApproval::Approved)
                    ->where('conversion_status', AudioConversionStatus::Completed),
                new EnsureUserHasSufficientBalanceForCall(),
            ],
            'phone_number' => ['required', 'phone:BD'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'caller_id' => [
                'description' => 'The ID of the caller to be used for this call. The caller must belong to the authenticated user and be enabled.',
                'example' => 1,
            ],
            'audio_id' => [
                'description' => 'The ID of the audio to be played during the call. The audio must belong to the authenticated user, be approved, and have completed conversion.',
                'example' => 1,
            ],
            'phone_number' => [
                'description' => 'The recipient phone number for the call. It must be a valid phone number format.',
                'example' => '+8801234567890',
            ],
            'scheduled_at' => [
                'description' => 'Optional. The date and time when the call should be scheduled. If not provided, the call will be made immediately.',
                'example' => now()->format('Y-m-d H:i:s'),
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
