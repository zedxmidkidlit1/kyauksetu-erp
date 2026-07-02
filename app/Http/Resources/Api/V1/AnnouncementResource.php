<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'body' => $this->body,
            'announcement_type' => $this->announcement_type,
            'priority' => $this->priority,
            'publish_at' => $this->publish_at?->toJSON(),
            'expires_at' => $this->expires_at?->toJSON(),
        ];
    }
}
