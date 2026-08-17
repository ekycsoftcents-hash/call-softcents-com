<?php

declare(strict_types=1);

namespace App\Support\Payment\Drivers;

use App\Contracts\PaymentGateway;
use App\Support\Payment\Data\PaymentRequest;
use App\Support\Payment\Data\PaymentResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final readonly class PipraPayDriver implements PaymentGateway
{
    /**
     * @throws ConnectionException
     */
    public function __construct(public array $config)
    {
        if (empty($config['api_key'])) {
            throw new ConnectionException('API Key is required');
        }

        if (empty($config['base_url'])) {
            throw new ConnectionException('Base URL is required');
        }
    }

    /**
     * @throws ConnectionException
     */
    public function create(PaymentRequest $request): PaymentResponse
    {
        $response = $this->client()->post('checkout/redirect', [
            'full_name' => $request->customerName,
            'email_address' => $request->customerEmail,
            'mobile_number' => $request->customerPhone,
            'amount' => (string) $request->amount,
            'currency' => $request->currency,
            'metadata' => json_encode($request->metadata),
            'return_url' => $request->returnUrl,
            'webhook_url' => $request->webhookUrl,
        ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error');

            throw new RuntimeException("PipraPay checkout failed: {$error}");
        }

        $data = $response->json();

        if (empty($data['pp_id'])) {
            throw new RuntimeException('PipraPay checkout did not return a pp_id.');
        }

        return new PaymentResponse(
            successful: $response->successful(),
            gatewayDriver: 'piprapay',
            gatewayStatus: 'pending',
            gatewayPaymentId: $data['pp_id'],
            checkoutUrl: $data['pp_url'],
            amount: $request->amount,
            currency: $request->currency,
            raw: $data,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function verify(array $payload): PaymentResponse
    {
        $response = $this->client()->post('verify-payment', ['pp_id' => $payload['pp_id']]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error');

            throw new RuntimeException("PipraPay verify failed: {$error}");
        }

        $data = $response->json();

        return new PaymentResponse(
            successful: $response->successful(),
            gatewayDriver: 'piprapay',
            gatewayStatus: $data['status'],
            gatewayPaymentId: $data['pp_id'],
            checkoutUrl: $data['currency'],
            amount: $data, currency: $data['currency'], raw: $data,
        );
    }

    public function handleWebhook(array $payload): PaymentResponse
    {
        return new PaymentResponse(
            successful: $payload['status'] === 'completed',
            gatewayDriver: 'piprapay',
            gatewayStatus: $payload['status'],
            gatewayPaymentId: $payload['pp_id'],
            checkoutUrl: $payload['currency'],
            amount: (int) $payload['amount'],
            currency: $payload['currency'],
            raw: $payload,
        );
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->config['base_url'])
            ->withHeaders(['MHS-PIPRAPAY-API-KEY' => $this->config['api_key']])
            ->acceptJson()
            ->asJson();
    }
}
