<?php

declare(strict_types=1);

namespace App\Support\Payment\Data;

final readonly class PaymentRequest
{
    public function __construct(
        public string $orderId,
        public int $amount,
        public string $currency = 'BDT',
        public ?string $customerName = null,
        public ?string $customerEmail = null,
        public ?string $customerPhone = null,
        public ?string $returnUrl = null,
        public ?string $cancelUrl = null,
        public ?string $webhookUrl = null,
        public array $metadata = [],
    ) {}
}
