<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CallerResource extends JsonResource
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
            'caller_name' => $this->caller_name,
            'caller_number' => $this->caller_number,
            'server_id' => $this->server_id,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
