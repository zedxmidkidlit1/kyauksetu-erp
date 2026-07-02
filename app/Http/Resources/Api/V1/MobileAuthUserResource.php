<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileAuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roles = $this->roles->pluck('name')->values()->all();
        $primaryRole = collect(['student', 'teacher'])
            ->first(fn (string $role): bool => in_array($role, $roles, true));

        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
            ],
            'roles' => $roles,
            'primary_role' => $primaryRole,
            'profile' => match ($primaryRole) {
                'student' => $this->studentProfileSummary(),
                'teacher' => $this->teacherProfileSummary(),
                default => null,
            },
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function studentProfileSummary(): ?array
    {
        if (! $this->studentProfile) {
            return null;
        }

        return [
            'type' => 'student',
            'id' => $this->studentProfile->id,
            'student_no' => $this->studentProfile->student_no,
            'roll_no' => $this->studentProfile->roll_no,
            'status' => $this->studentProfile->status,
            'program' => $this->studentProfile->program ? [
                'id' => $this->studentProfile->program->id,
                'name' => $this->studentProfile->program->name,
                'code' => $this->studentProfile->program->code,
            ] : null,
            'major' => $this->studentProfile->major ? [
                'id' => $this->studentProfile->major->id,
                'name' => $this->studentProfile->major->name,
                'code' => $this->studentProfile->major->code,
            ] : null,
            'class_section' => $this->studentProfile->classSection ? [
                'id' => $this->studentProfile->classSection->id,
                'name' => $this->studentProfile->classSection->name,
                'section' => $this->studentProfile->classSection->section,
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function teacherProfileSummary(): ?array
    {
        if (! $this->teacherProfile) {
            return null;
        }

        return [
            'type' => 'teacher',
            'id' => $this->teacherProfile->id,
            'staff_no' => $this->teacherProfile->staff_no,
            'institutional_email' => $this->teacherProfile->institutional_email,
            'position' => $this->teacherProfile->position,
            'rank' => $this->teacherProfile->rank,
            'status' => $this->teacherProfile->status,
            'department' => $this->teacherProfile->department ? [
                'id' => $this->teacherProfile->department->id,
                'name' => $this->teacherProfile->department->name,
                'code' => $this->teacherProfile->department->code,
            ] : null,
        ];
    }
}
