<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTransactions
{
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
