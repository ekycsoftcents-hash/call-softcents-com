<?php

declare(strict_types=1);

namespace App\Support\Payment;

use App\Contracts\PaymentGateway;
use App\Support\Payment\Drivers\PipraPayDriver;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Manager;

final class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('payment.default');
    }

    /**
     * @throws ConnectionException
     */
    protected function createPiprapayDriver(): PaymentGateway
    {
        return new PipraPayDriver($this->config->get('payment.drivers.piprapay'));
    }
}
