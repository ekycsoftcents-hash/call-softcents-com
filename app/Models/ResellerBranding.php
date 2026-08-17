<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Guarded(['id'])]
final class ResellerBranding extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }
}
