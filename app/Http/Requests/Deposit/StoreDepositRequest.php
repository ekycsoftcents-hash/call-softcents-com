<?php

declare(strict_types=1);

namespace App\Http\Requests\Deposit;

use Illuminate\Foundation\Http\FormRequest;

final class StoreDepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:10'],
            'gateway' => ['required', 'string', 'in:piprapay'],
            'currency' => ['nullable', 'string', 'in:BDT'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'amount' => [
                'description' => 'The amount to deposit.',
                'example' => 100,
            ],
            'gateway' => [
                'description' => 'The payment gateway to use.',
                'example' => 'piprapay',
            ],
            'currency' => [
                'description' => 'The currency of the deposit.',
                'example' => 'BDT',
            ],
        ];
    }
}
