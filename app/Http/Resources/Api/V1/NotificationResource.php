<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Announcement $announcement */
        $announcement = $this->resource;

        return [
            'id' => "announcement:{$announcement->id}",
            'type' => 'announcement',
            'title' => $announcement->title,
            'body' => $announcement->body,
            'priority' => $announcement->priority,
            'published_at' => $announcement->publish_at?->toJSON(),
            'expires_at' => $announcement->expires_at?->toJSON(),
            'read_at' => null,
            'data' => [
                'announcement_id' => $announcement->id,
                'announcement_type' => $announcement->announcement_type,
            ],
        ];
    }
}
