<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
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
            'student_no' => $this->student_no,
            'roll_no' => $this->roll_no,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'institutional_email' => $this->institutional_email,
            'phone' => $this->phone,
            'status' => $this->status,
            'enrolled_at' => $this->enrolled_at?->toDateString(),
            'department' => $this->whenLoaded('department', fn () => $this->department ? [
                'id' => $this->department->id,
                'name' => $this->department->name,
                'code' => $this->department->code,
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
            'academic_year' => $this->whenLoaded('academicYear', fn () => $this->academicYear ? [
                'id' => $this->academicYear->id,
                'name' => $this->academicYear->name,
            ] : null),
            'class_section' => $this->whenLoaded('classSection', fn () => $this->classSection ? [
                'id' => $this->classSection->id,
                'name' => $this->classSection->name,
                'section' => $this->classSection->section,
            ] : null),
        ];
    }
}
