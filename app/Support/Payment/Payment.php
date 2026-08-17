<?php

declare(strict_types=1);

namespace App\Support\Payment;

use Illuminate\Support\Facades\Facade;

/**
 * @see PaymentManager
 */
final class Payment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PaymentManager::class;
    }
}
