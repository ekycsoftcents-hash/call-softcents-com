<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TTSEngine;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TTSLanguage extends Model
{
    use HasFactory;

    protected $table = 'tts_languages';

    protected $fillable = [
        'name',
        'code',
        'engine',
        'enabled',
    ];

    public function ttsArtists(): HasMany
    {
        return $this->hasMany(TTSArtist::class, 'tts_language_id');
    }

    protected function casts(): array
    {
        return [
            'engine' => TTSEngine::class,
        ];
    }

    #[Scope]
    protected function enabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}
