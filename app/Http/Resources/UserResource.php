<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'balance' => $this->balance,
            'pulse_rate' => $this->pulse_rate,
            'pulse_duration' => $this->pulse_duration,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
