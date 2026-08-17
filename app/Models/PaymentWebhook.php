<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['id'])]
class PaymentWebhook extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'headers' => 'array',
        ];
    }
}
