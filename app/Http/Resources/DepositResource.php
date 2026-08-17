<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DepositResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'gateway' => $this->gateway,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'checkout_url' => $this->meta_data['checkout_url'] ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
