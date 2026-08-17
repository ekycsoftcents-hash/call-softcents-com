<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TemplateApproval;
use App\Models\Scopes\OwnedByAuthUser;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(OwnedByAuthUser::class)]
final class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'approval',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'approval' => TemplateApproval::class,
        ];
    }
}
