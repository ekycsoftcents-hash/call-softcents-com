<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasTransactions;
use App\Contracts\Transactionable;
use App\Enums\DepositPaymentMethod;
use App\Enums\DepositStatus;
use Database\Factories\DepositFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Guarded(['id'])]
final class Deposit extends Model implements Transactionable
{
    /** @use HasFactory<DepositFactory> */
    use HasFactory, HasTransactions, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'payment_method' => DepositPaymentMethod::class,
            'status' => DepositStatus::class,
            'meta' => 'array',
            'paid_at' => 'datetime',
        ];
    }
}
