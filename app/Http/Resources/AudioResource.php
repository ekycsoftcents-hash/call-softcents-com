<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Audio;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Audio
 */
final class AudioResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'approval' => $this->approval,
            'message' => $this->message,
            'tts_artist' => $this->whenLoaded('ttsArtist'),
            'original_url' => $this->original_path ? getFileUrl($this->original_path) : null,
            'converted_url' => $this->converted_path ? getFileUrl($this->converted_path) : null,
            'duration' => $this->duration,
            'size' => $this->size,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
