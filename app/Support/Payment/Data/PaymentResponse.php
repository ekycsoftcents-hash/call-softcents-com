<?php

declare(strict_types=1);

namespace App\Support\Payment\Data;

final readonly class PaymentResponse
{
    public function __construct(
        public bool $successful,
        public string $gatewayDriver,
        public string $gatewayStatus,
        public ?string $gatewayPaymentId = null,
        public ?string $checkoutUrl = null,
        public int|float|null $amount = null,
        public ?string $currency = 'BDT',
        public array $raw = [],
        public ?string $message = null,
    ) {}
}
