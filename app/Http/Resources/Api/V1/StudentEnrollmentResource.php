<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [];
        }

        return [
            'id' => $this->id,
            'roll_no' => $this->roll_no,
            'year_level' => $this->year_level,
            'status' => $this->status,
            'enrolled_at' => $this->enrolled_at?->toDateString(),
            'completed_at' => $this->completed_at?->toDateString(),
            'academic_year' => $this->whenLoaded('academicYear', fn () => $this->academicYear ? [
                'id' => $this->academicYear->id,
                'name' => $this->academicYear->name,
            ] : null),
            'semester' => $this->whenLoaded('semester', fn () => $this->semester ? [
                'id' => $this->semester->id,
                'name' => $this->semester->name,
            ] : null),
            'program' => $this->whenLoaded('program', fn () => $this->program ? [
                'id' => $this->program->id,
                'name' => $this->program->name,
                'code' => $this->program->code,
            ] : null),
            'major' => $this->whenLoaded('major', fn () => $this->major ? [
                'id' => $this->major->id,
                'name' => $this->major->name,
                'code' => $this->major->code,
            ] : null),
            'class_section' => $this->whenLoaded('classSection', fn () => $this->classSection ? [
                'id' => $this->classSection->id,
                'name' => $this->classSection->name,
                'section' => $this->classSection->section,
            ] : null),
        ];
    }
}
