<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Support\Payment\Data\PaymentRequest;
use App\Support\Payment\Data\PaymentResponse;

interface PaymentGateway
{
    public function create(PaymentRequest $request): PaymentResponse;

    public function verify(array $payload): PaymentResponse;

    public function handleWebhook(array $payload): PaymentResponse;
}
