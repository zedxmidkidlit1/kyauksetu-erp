<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimetableResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            'effective_from' => $this->effective_from?->toDateString(),
            'effective_until' => $this->effective_until?->toDateString(),
            'academic_year' => $this->whenLoaded('academicYear', fn () => $this->academicYear ? [
                'id' => $this->academicYear->id,
                'name' => $this->academicYear->name,
            ] : null),
            'semester' => $this->whenLoaded('semester', fn () => $this->semester ? [
                'id' => $this->semester->id,
                'name' => $this->semester->name,
            ] : null),
            'class_section' => $this->whenLoaded('classSection', fn () => $this->classSection ? [
                'id' => $this->classSection->id,
                'name' => $this->classSection->name,
                'section' => $this->classSection->section,
            ] : null),
            'slots' => $this->whenLoaded('slots', fn () => $this->slots->map(fn ($slot): array => [
                'id' => $slot->id,
                'day_of_week' => $slot->day_of_week,
                'starts_at' => $slot->starts_at,
                'ends_at' => $slot->ends_at,
                'slot_type' => $slot->slot_type,
                'status' => $slot->status,
                'course' => $slot->course ? [
                    'id' => $slot->course->id,
                    'name' => $slot->course->name,
                    'code' => $slot->course->code,
                ] : null,
                'teacher' => $slot->teacherProfile ? [
                    'id' => $slot->teacherProfile->id,
                    'name' => $slot->teacherProfile->user?->name,
                    'staff_no' => $slot->teacherProfile->staff_no,
                ] : null,
                'room' => $slot->room ? [
                    'id' => $slot->room->id,
                    'name' => $slot->room->name,
                    'code' => $slot->room->code,
                ] : null,
            ])->values()),
        ];
    }
}
