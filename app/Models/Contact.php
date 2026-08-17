<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

final class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    protected $fillable = [
        'group_id',
        'phone_number',
    ];

    protected $casts = [
        'phone_number' => E164PhoneNumberCast::class,
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->phone_number
        );
    }

    protected function nameOrNumber(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name ?: $this->phone_number,
        );
    }
}
