<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Department;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Kai\Contracts\AiResponder;
use App\Services\Kai\ExternalAiResponder;
use App\Services\Kai\LocalAiResponder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KaiProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_responder_remains_default(): void
    {
        config(['kai.responder' => 'local']);

        $this->assertInstanceOf(LocalAiResponder::class, app(AiResponder::class));
    }

    public function test_provider_binding_can_be_switched_by_config(): void
    {
        config(['kai.responder' => 'external']);

        $this->assertInstanceOf(ExternalAiResponder::class, app(AiResponder::class));
    }

    public function test_missing_provider_config_falls_back_without_exposing_errors_or_secrets(): void
    {
        config([
            'kai.responder' => 'external',
            'kai.provider.enabled' => true,
            'kai.provider.endpoint' => null,
            'kai.provider.api_key' => 'super-secret-test-key',
        ]);

        $response = app(AiResponder::class)->respond('Show my fees', [
            'unpaid_due_fees' => [
                'count' => 1,
                'total_payable_amount' => '5000.00',
            ],
        ]);

        $this->assertSame('You have 1 unpaid or due fee item(s) totaling 5000.00.', $response['reply']);
        $this->assertStringNotContainsString('super-secret-test-key', json_encode($response, JSON_THROW_ON_ERROR));
    }

    public function test_chat_endpoint_still_returns_safe_compact_json(): void
    {
        $user = $this->createStudentUser();

        Sanctum::actingAs($user);

        $this
            ->postJson('/api/v1/kai/chat', ['message' => 'Hello KAI'])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'reply',
                    'context_used' => ['keys'],
                    'suggestions',
                ],
            ])
            ->assertJsonMissingPath('data.context_used.student_profile')
            ->assertJsonMissingPath('data.context_used.user.email');
    }

    private function createStudentUser(): User
    {
        Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'email' => 'kai-provider-student@example.test',
        ]);
        $user->assignRole('student');

        $department = Department::create([
            'code' => 'KAI',
            'name' => 'KAI Department',
            'is_active' => true,
        ]);
        $program = Program::create([
            'code' => 'KAI-P',
            'name' => 'KAI Program',
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $major = Major::create([
            'department_id' => $department->id,
            'program_id' => $program->id,
            'code' => 'KAI-M',
            'name' => 'KAI Major',
            'status' => 'active',
        ]);
        $academicYear = AcademicYear::create([
            'name' => '2026-2027 KAI',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'status' => 'active',
        ]);
        $classSection = ClassSection::create([
            'academic_year_id' => $academicYear->id,
            'major_id' => $major->id,
            'name' => 'KAI Class',
            'year_level' => 1,
            'section' => 'KAI',
            'status' => 'active',
        ]);

        StudentProfile::create([
            'user_id' => $user->id,
            'student_no' => 'KAI-STU-001',
            'roll_no' => 'KAI-STU-001',
            'institutional_email' => 'kai-provider-student@school.test',
            'first_name' => 'KAI',
            'last_name' => 'Student',
            'department_id' => $department->id,
            'program_id' => $program->id,
            'major_id' => $major->id,
            'academic_year_id' => $academicYear->id,
            'class_section_id' => $classSection->id,
            'admission_year' => 2026,
            'status' => 'active',
            'enrolled_at' => '2026-06-01',
        ]);

        return $user;
    }
}
