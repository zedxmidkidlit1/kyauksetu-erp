<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceRecordResource extends JsonResource
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
            'status' => $this->status,
            'marked_at' => $this->marked_at?->toJSON(),
            'remarks' => $this->remarks,
            'enrollment_id' => $this->student_enrollment_id,
            'session' => $this->whenLoaded('attendanceSession', fn () => $this->attendanceSession ? [
                'id' => $this->attendanceSession->id,
                'session_date' => $this->attendanceSession->session_date?->toDateString(),
                'starts_at' => $this->attendanceSession->starts_at,
                'ends_at' => $this->attendanceSession->ends_at,
                'status' => $this->attendanceSession->status,
                'course' => $this->attendanceSession->course ? [
                    'id' => $this->attendanceSession->course->id,
                    'name' => $this->attendanceSession->course->name,
                    'code' => $this->attendanceSession->course->code,
                ] : null,
                'teacher' => $this->attendanceSession->teacherProfile ? [
                    'id' => $this->attendanceSession->teacherProfile->id,
                    'name' => $this->attendanceSession->teacherProfile->user?->name,
                    'staff_no' => $this->attendanceSession->teacherProfile->staff_no,
                ] : null,
                'room' => $this->attendanceSession->room ? [
                    'id' => $this->attendanceSession->room->id,
                    'name' => $this->attendanceSession->room->name,
                    'code' => $this->attendanceSession->room->code,
                ] : null,
            ] : null),
        ];
    }
}
