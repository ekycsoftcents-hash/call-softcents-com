<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Audio;
use App\Models\Group;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Number;
use Illuminate\Translation\PotentiallyTranslatedString;

final readonly class EnsureUserHasSufficientBalanceForCampaign implements ValidationRule
{
    public function __construct(private ?int $groupId = null) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $audio = Audio::find($value);
        if (! $audio) {
            return;
        }

        if ($this->groupId === null) {
            return;
        }

        $group = Group::withCount('contacts')->find($this->groupId);
        if (! $group) {
            return;
        }

        $contactsCount = (int) $group->contacts_count;
        if ($contactsCount === 0) {
            return;
        }

        $costPerCall = $audio->cost;
        $totalEstimatedCost = $costPerCall * $contactsCount;

        if (! $user->hasEnoughBalance($totalEstimatedCost)) {
            $message = sprintf(
                'Insufficient balance. This campaign costs estimated %s for %s calls. Your balance: %s.',
                Number::currency($totalEstimatedCost, 'BDT'),
                Number::format($contactsCount),
                Number::currency($user->balance, 'BDT')
            );

            $fail($message);
        }
    }
}
