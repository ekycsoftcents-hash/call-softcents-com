<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ServerFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded(['id'])]
#[Hidden(['ari_password', 'database_password', 'freeswitch_event_socket_password'])]
final class Server extends Model
{
    /** @use HasFactory<ServerFactory> */
    use HasFactory;

    protected $casts = [
        'ari_password' => 'encrypted',
        'database_password' => 'encrypted',
        'freeswitch_event_socket_password' => 'encrypted',
        'ari_port' => 'integer',
    ];

    public function callers(): HasMany
    {
        return $this->hasMany(Caller::class);
    }

    public function fusionPbxEventSocketHost(): string
    {
        return $this->freeswitch_event_socket_host ?: $this->fusionpbx_domain;
    }


    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}
