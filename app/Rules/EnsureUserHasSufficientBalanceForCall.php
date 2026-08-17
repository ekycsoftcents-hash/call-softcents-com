<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Audio;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Number;
use Illuminate\Translation\PotentiallyTranslatedString;

final class EnsureUserHasSufficientBalanceForCall implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();
        $audio = Audio::find($value);

        if (! $user) {
            $fail('User not authenticated.');

            return;
        }

        if (! $audio) {
            $fail('The selected audio does not exist.');

            return;
        }

        if (! $user->hasEnoughBalance($audio->cost)) {
            $message = sprintf(
                'Insufficient balance. This call costs %s. Your balance: %s.',
                Number::currency($audio->cost, 'BDT'),
                Number::currency($user->balance, 'BDT')
            );

            $fail($message);
        }
    }
}
