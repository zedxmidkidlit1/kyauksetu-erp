<?php

namespace App\Services\Mobile;

use App\Models\Announcement;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class VisibleAnnouncementQuery
{
    public function forUser(User $user): Builder
    {
        if ($user->hasRole('student')) {
            $profile = $user->studentProfile()->first();

            abort_unless($profile instanceof StudentProfile, 403, 'This account is not linked to a student profile.');

            return $this->forStudent($profile, $user);
        }

        if ($user->hasRole('teacher')) {
            $profile = $user->teacherProfile()->first();

            abort_unless($profile instanceof TeacherProfile, 403, 'This account is not linked to a teacher profile.');

            return $this->forTeacher($profile, $user);
        }

        abort(403, 'Announcements are only available for supported student or teacher accounts.');
    }

    private function forStudent(StudentProfile $profile, User $user): Builder
    {
        return $this->baseQuery()
            ->whereHas('audiences', function (Builder $query) use ($profile, $user): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('audience_type', 'role')
                            ->where('role_name', 'student');
                    })
                    ->orWhere(function (Builder $query) use ($user): void {
                        $query
                            ->where('audience_type', 'user')
                            ->where('user_id', $user->id);
                    });

                if ($profile->department_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'department')
                            ->where('department_id', $profile->department_id);
                    });
                }

                if ($profile->program_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'program')
                            ->where('program_id', $profile->program_id);
                    });
                }

                if ($profile->major_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'major')
                            ->where('major_id', $profile->major_id);
                    });
                }

                if ($profile->class_section_id) {
                    $query->orWhere(function (Builder $query) use ($profile): void {
                        $query
                            ->where('audience_type', 'class_section')
                            ->where('class_section_id', $profile->class_section_id);
                    });
                }
            });
    }

    private function forTeacher(TeacherProfile $profile, User $user): Builder
    {
        $departmentId = $profile->department_id;
        $programIds = $this->assignedIds($profile, 'program_id');
        $majorIds = $this->assignedIds($profile, 'major_id');
        $classSectionIds = $this->assignedIds($profile, 'class_section_id');

        return $this->baseQuery()
            ->whereHas('audiences', function (Builder $query) use ($user, $departmentId, $programIds, $majorIds, $classSectionIds): void {
                $query
                    ->where('audience_type', 'all')
                    ->orWhere(function (Builder $query): void {
                        $query
                            ->where('audience_type', 'role')
                            ->where('role_name', 'teacher');
                    })
                    ->orWhere(function (Builder $query) use ($user): void {
                        $query
                            ->where('audience_type', 'user')
                            ->where('user_id', $user->id);
                    });

                if ($departmentId) {
                    $query->orWhere(function (Builder $query) use ($departmentId): void {
                        $query
                            ->where('audience_type', 'department')
                            ->where('department_id', $departmentId);
                    });
                }

                if ($programIds !== []) {
                    $query->orWhere(function (Builder $query) use ($programIds): void {
                        $query
                            ->where('audience_type', 'program')
                            ->whereIn('program_id', $programIds);
                    });
                }

                if ($majorIds !== []) {
                    $query->orWhere(function (Builder $query) use ($majorIds): void {
                        $query
                            ->where('audience_type', 'major')
                            ->whereIn('major_id', $majorIds);
                    });
                }

                if ($classSectionIds !== []) {
                    $query->orWhere(function (Builder $query) use ($classSectionIds): void {
                        $query
                            ->where('audience_type', 'class_section')
                            ->whereIn('class_section_id', $classSectionIds);
                    });
                }
            });
    }

    private function baseQuery(): Builder
    {
        return Announcement::query()
            ->where('status', 'published')
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * @return array<int, int>
     */
    private function assignedIds(TeacherProfile $profile, string $column): array
    {
        return $profile
            ->teachingAssignments()
            ->whereNotNull($column)
            ->pluck($column)
            ->unique()
            ->values()
            ->all();
    }
}
