<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\AdmissionBatch;
use App\Models\Applicant;
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
