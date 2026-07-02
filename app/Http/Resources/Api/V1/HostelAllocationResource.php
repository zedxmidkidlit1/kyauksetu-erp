<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HostelAllocationResource extends JsonResource
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
            'allocated_at' => $this->allocated_at?->toJSON(),
            'vacated_at' => $this->vacated_at?->toJSON(),
            'allocation_status' => $this->allocation_status,
            'hostel' => $this->whenLoaded('hostel', fn () => $this->hostel ? [
                'id' => $this->hostel->id,
                'name' => $this->hostel->name,
                'code' => $this->hostel->code,
            ] : null),
            'room' => $this->whenLoaded('hostelRoom', fn () => $this->hostelRoom ? [
                'id' => $this->hostelRoom->id,
                'name' => $this->hostelRoom->name,
                'room_no' => $this->hostelRoom->room_no,
                'floor' => $this->hostelRoom->floor,
            ] : null),
            'bed' => $this->whenLoaded('hostelBed', fn () => $this->hostelBed ? [
                'id' => $this->hostelBed->id,
                'bed_no' => $this->hostelBed->bed_no,
                'bed_status' => $this->hostelBed->bed_status,
            ] : null),
        ];
    }
}
