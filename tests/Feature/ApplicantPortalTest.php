<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\AdmissionBatch;
use App\Models\Applicant;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_register(): void
    {
        $response = $this->post(route('applicant.register.store'), [
            'first_name' => 'Aye',
            'last_name' => 'Chan',
            'email' => 'aye.chan@example.test',
            'phone' => '099999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('applicant.dashboard'));
        $this->assertAuthenticated();
        $user = User::where('email', 'aye.chan@example.test')->firstOrFail();

        $this->assertDatabaseHas('applicants', [
            'user_id' => $user->id,
            'email' => 'aye.chan@example.test',
            'first_name' => 'Aye',
            'last_name' => 'Chan',
        ]);

        $this->assertTrue($user->hasRole('applicant'));
    }

    public function test_guest_is_redirected_to_applicant_login(): void
    {
        $this
            ->get(route('applicant.dashboard'))
            ->assertRedirect(route('applicant.login'));
    }

    public function test_applicant_can_create_own_application(): void
    {
        [$user, $applicant] = $this->createApplicantUser('mg.mg@example.test');
        $batch = AdmissionBatch::create([
            'name' => '2026 Main Intake',
            'status' => 'open',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('applicant.applications.store'), [
                'admission_batch_id' => $batch->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admission_applications', [
            'admission_batch_id' => $batch->id,
            'applicant_id' => $applicant->id,
            'application_status' => 'submitted',
        ]);
    }

    public function test_applicant_cannot_view_another_applicants_application(): void
    {
        [$user] = $this->createApplicantUser('owner@example.test');
        [, $otherApplicant] = $this->createApplicantUser('other@example.test');
        $batch = AdmissionBatch::create([
            'name' => '2026 Main Intake',
            'status' => 'open',
        ]);
        $application = AdmissionApplication::create([
            'admission_batch_id' => $batch->id,
            'applicant_id' => $otherApplicant->id,
            'application_no' => 'ADM-OTHER',
            'application_status' => 'submitted',
        ]);

        $this
            ->actingAs($user)
            ->get(route('applicant.applications.show', $application))
            ->assertForbidden();
    }

    public function test_applicant_cannot_apply_outside_the_batch_window(): void
    {
        $this->travelTo('2026-07-10 12:00:00');
        [$user] = $this->createApplicantUser('window@example.test');
        $batch = AdmissionBatch::create([
            'name' => 'Future Intake',
            'opens_at' => '2026-07-11',
            'closes_at' => '2026-07-31',
            'status' => 'open',
        ]);

        $this
            ->actingAs($user)
            ->post(route('applicant.applications.store'), [
                'admission_batch_id' => $batch->id,
            ])
            ->assertSessionHasErrors('admission_batch_id');

        $this->assertDatabaseMissing('admission_applications', [
            'admission_batch_id' => $batch->id,
        ]);
    }

    public function test_applicant_cannot_submit_the_same_programless_application_twice(): void
    {
        [$user, $applicant] = $this->createApplicantUser('duplicate@example.test');
        $batch = AdmissionBatch::create([
            'name' => 'General Intake',
            'status' => 'open',
        ]);

        $this->actingAs($user)->post(route('applicant.applications.store'), [
            'admission_batch_id' => $batch->id,
        ])->assertRedirect();

        $this->actingAs($user)->post(route('applicant.applications.store'), [
            'admission_batch_id' => $batch->id,
        ])->assertSessionHasErrors([
            'admission_batch_id' => 'You already have an application for this batch and program.',
        ]);

        $applications = AdmissionApplication::query()
            ->whereBelongsTo($applicant)
            ->whereBelongsTo($batch)
            ->get();

        $this->assertCount(1, $applications);
        $this->assertNull($applications->sole()->program_id);
    }

    public function test_applicant_cannot_apply_to_a_batch_for_an_inactive_program(): void
    {
        [$user] = $this->createApplicantUser('inactive-program@example.test');
        $program = Program::create([
            'name' => 'Retired Program',
            'code' => 'RETIRED',
            'duration_years' => 4,
            'status' => 'inactive',
        ]);
        $batch = AdmissionBatch::create([
            'program_id' => $program->id,
            'name' => 'Retired Intake',
            'status' => 'open',
        ]);

        $this->actingAs($user)->post(route('applicant.applications.store'), [
            'admission_batch_id' => $batch->id,
        ])->assertSessionHasErrors('admission_batch_id');

        $this->assertDatabaseMissing('admission_applications', [
            'admission_batch_id' => $batch->id,
        ]);
    }

    public function test_applicant_cannot_override_a_batch_program(): void
    {
        [$user] = $this->createApplicantUser('program@example.test');
        $batchProgram = Program::create([
            'name' => 'Engineering',
            'code' => 'ENG',
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $otherProgram = Program::create([
            'name' => 'Business',
            'code' => 'BUS',
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $batch = AdmissionBatch::create([
            'program_id' => $batchProgram->id,
            'name' => 'Engineering Intake',
            'status' => 'open',
        ]);

        $this
            ->actingAs($user)
            ->post(route('applicant.applications.store'), [
                'admission_batch_id' => $batch->id,
                'program_id' => $otherProgram->id,
            ])
            ->assertSessionHasErrors('program_id');
    }

    public function test_applicant_cannot_select_a_major_from_another_program(): void
    {
        [$user] = $this->createApplicantUser('major@example.test');
        $department = Department::create([
            'name' => 'Technology',
            'code' => 'TECH',
            'is_active' => true,
        ]);
        $batchProgram = Program::create([
            'name' => 'Engineering',
            'code' => 'ENG-MAJOR',
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $otherProgram = Program::create([
            'name' => 'Business',
            'code' => 'BUS-MAJOR',
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $otherMajor = Major::create([
            'department_id' => $department->id,
            'program_id' => $otherProgram->id,
            'name' => 'Accounting',
            'code' => 'ACC',
            'status' => 'active',
        ]);
        $batch = AdmissionBatch::create([
            'program_id' => $batchProgram->id,
            'name' => 'Engineering Intake',
            'status' => 'open',
        ]);

        $this
            ->actingAs($user)
            ->post(route('applicant.applications.store'), [
                'admission_batch_id' => $batch->id,
                'major_id' => $otherMajor->id,
            ])
            ->assertSessionHasErrors('major_id');
    }

    /**
     * @return array{0: User, 1: Applicant}
     */
    private function createApplicantUser(string $email): array
    {
        Role::firstOrCreate([
            'name' => 'applicant',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'email' => $email,
        ]);
        $user->assignRole('applicant');

        $applicant = Applicant::create([
            'user_id' => $user->id,
            'applicant_no' => 'APP-'.strtoupper(strtok($email, '@')),
            'first_name' => 'Applicant',
            'last_name' => 'User',
            'email' => $email,
            'status' => 'active',
        ]);

        return [$user, $applicant];
    }
}
