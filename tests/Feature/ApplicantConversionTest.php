<?php

namespace Tests\Feature;

use App\Filament\Resources\AdmissionApplications\Pages\ListAdmissionApplications;
use App\Models\AcademicYear;
use App\Models\AdmissionApplication;
use App\Models\AdmissionBatch;
use App\Models\AdmissionDecision;
use App\Models\Applicant;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class ApplicantConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_convert_accepted_applicant_to_student(): void
    {
        [$application, $applicantUser, $adminUser] = $this->createAcceptedApplication();

        Livewire::actingAs($adminUser)
            ->test(ListAdmissionApplications::class)
            ->callAction(TestAction::make('convertToStudent')->table($application))
            ->assertNotified('Applicant converted');

        $application->refresh();
        $applicantUser->refresh();

        $studentProfile = StudentProfile::query()->where('user_id', $applicantUser->id)->firstOrFail();
        $studentEnrollment = StudentEnrollment::query()->whereBelongsTo($studentProfile)->firstOrFail();

        $this->assertTrue($applicantUser->hasRole('student'));
        $this->assertSame($studentProfile->id, $application->student_profile_id);
        $this->assertSame($adminUser->id, $application->converted_by);
        $this->assertNotNull($application->converted_at);
        $this->assertSame('Aye', $studentProfile->first_name);
        $this->assertSame('Chan', $studentProfile->last_name);
        $this->assertSame($application->academic_year_id, $studentEnrollment->academic_year_id);
        $this->assertSame($application->program_id, $studentEnrollment->program_id);
        $this->assertSame($application->major_id, $studentEnrollment->major_id);
        $this->assertSame('active', $studentEnrollment->status);
    }

    public function test_duplicate_conversion_is_prevented(): void
    {
        [$application, , $adminUser] = $this->createAcceptedApplication();

        $application->convertToStudent($adminUser);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('already been converted');

        $application->refresh()->convertToStudent($adminUser);
    }

    /**
     * @return array{0: AdmissionApplication, 1: User, 2: User}
     */
    private function createAcceptedApplication(): array
    {
        Role::firstOrCreate([
            'name' => 'applicant',
            'guard_name' => 'web',
        ]);
        Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $adminUser = User::factory()->create([
            'email' => 'admin@example.test',
        ]);
        $adminUser->assignRole('super_admin');

        $applicantUser = User::factory()->create([
            'name' => 'Aye Chan',
            'email' => 'aye.chan@example.test',
        ]);
        $applicantUser->assignRole('applicant');

        $department = Department::create([
            'code' => 'IT',
            'name' => 'Information Technology',
            'is_active' => true,
        ]);
        $program = Program::create([
            'name' => 'Bachelor of Computer Science',
            'code' => 'BCS',
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $major = Major::create([
            'department_id' => $department->id,
            'program_id' => $program->id,
            'name' => 'Software Engineering',
            'code' => 'SE',
            'status' => 'active',
        ]);
        $academicYear = AcademicYear::create([
            'name' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'status' => 'active',
        ]);
        $batch = AdmissionBatch::create([
            'academic_year_id' => $academicYear->id,
            'program_id' => $program->id,
            'name' => '2026 Main Intake',
            'code' => 'ADM-2026',
            'status' => 'open',
        ]);
        $applicant = Applicant::create([
            'user_id' => $applicantUser->id,
            'applicant_no' => 'APP-2026-000001',
            'first_name' => 'Aye',
            'last_name' => 'Chan',
            'email' => $applicantUser->email,
            'phone' => '099999999',
            'date_of_birth' => '2008-01-15',
            'status' => 'active',
        ]);
        $application = AdmissionApplication::create([
            'admission_batch_id' => $batch->id,
            'applicant_id' => $applicant->id,
            'academic_year_id' => $academicYear->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'application_no' => 'ADM-2026-000001',
            'applied_at' => now(),
            'application_status' => 'accepted',
        ]);

        AdmissionDecision::create([
            'admission_application_id' => $application->id,
            'decision_status' => 'accepted',
            'decided_by' => $adminUser->id,
            'decided_at' => now(),
        ]);

        return [$application, $applicantUser, $adminUser];
    }
}
