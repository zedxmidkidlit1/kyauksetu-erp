<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Department;
use App\Models\KaiChatMessage;
use App\Models\KaiChatSession;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KaiChatLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_request_creates_session_and_messages(): void
    {
        $user = $this->createStudentUser('LOG');

        Sanctum::actingAs($user);

        $this
            ->withHeader('X-Request-Id', 'req-log-001')
            ->postJson('/api/v1/kai/chat', ['message' => 'Show my fees'])
            ->assertOk()
            ->assertJsonPath('data.reply', 'You have 0 unpaid or due fee item(s) totaling 0.00.');

        $session = KaiChatSession::query()->whereBelongsTo($user)->sole();

        $this->assertSame('Show my fees', $session->title);
        $this->assertSame('local', $session->driver);
        $this->assertSame('active', $session->status);
        $this->assertNotNull($session->last_message_at);

        $messages = $session->messages()->orderBy('id')->get();

        $this->assertCount(2, $messages);
        $this->assertSame('user', $messages[0]->role);
        $this->assertSame('Show my fees', $messages[0]->content);
        $this->assertNull($messages[0]->context_keys);
        $this->assertSame('assistant', $messages[1]->role);
        $this->assertSame('You have 0 unpaid or due fee item(s) totaling 0.00.', $messages[1]->content);
        $this->assertContains('student_profile', $messages[1]->context_keys);
        $this->assertSame(['request_id' => 'req-log-001'], $messages[1]->metadata);
    }

    public function test_context_keys_are_stored_without_raw_full_context_or_secrets(): void
    {
        $user = $this->createStudentUser('SAFE');

        Sanctum::actingAs($user);

        $this
            ->postJson('/api/v1/kai/chat', ['message' => 'api_key=secret-provider-key Show my fees'])
            ->assertOk();

        $assistantMessage = KaiChatMessage::query()
            ->whereBelongsTo($user)
            ->where('role', 'assistant')
            ->sole();
        $userMessage = KaiChatMessage::query()
            ->whereBelongsTo($user)
            ->where('role', 'user')
            ->sole();

        $this->assertContains('student_profile', $assistantMessage->context_keys);
        $this->assertNotContains('STU-SAFE', $assistantMessage->context_keys);
        $this->assertStringNotContainsString('student-SAFE@school.test', json_encode([
            'assistant_content' => $assistantMessage->content,
            'assistant_context_keys' => $assistantMessage->context_keys,
            'assistant_metadata' => $assistantMessage->metadata,
            'session_metadata' => $assistantMessage->session->metadata,
        ], JSON_THROW_ON_ERROR));
        $this->assertStringNotContainsString('secret-provider-key', $userMessage->content);
        $this->assertStringContainsString('api_key=[redacted]', $userMessage->content);
    }

    public function test_another_student_cannot_affect_another_students_chat_logs_through_api(): void
    {
        $firstUser = $this->createStudentUser('ONE');
        $secondUser = $this->createStudentUser('TWO');

        Sanctum::actingAs($firstUser);
        $this->postJson('/api/v1/kai/chat', ['message' => 'Show my timetable'])->assertOk();

        Sanctum::actingAs($secondUser);
        $this->postJson('/api/v1/kai/chat', ['message' => 'Show my timetable'])->assertOk();

        $firstSession = KaiChatSession::query()->whereBelongsTo($firstUser)->sole();
        $secondSession = KaiChatSession::query()->whereBelongsTo($secondUser)->sole();

        $this->assertNotSame($firstSession->id, $secondSession->id);
        $this->assertSame(2, KaiChatMessage::query()->whereBelongsTo($firstUser)->count());
        $this->assertSame(2, KaiChatMessage::query()->whereBelongsTo($secondUser)->count());
        $this->assertSame(0, $firstSession->messages()->where('user_id', $secondUser->id)->count());
        $this->assertSame(0, $secondSession->messages()->where('user_id', $firstUser->id)->count());
    }

    public function test_chat_endpoint_still_returns_compact_safe_json(): void
    {
        $user = $this->createStudentUser('JSON');

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

    private function createStudentUser(string $suffix): User
    {
        Role::firstOrCreate([
            'name' => 'student',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'name' => "Student {$suffix}",
            'email' => "student-{$suffix}@example.test",
        ]);
        $user->assignRole('student');

        $department = Department::create([
            'code' => "KAI{$suffix}",
            'name' => "KAI Department {$suffix}",
            'is_active' => true,
        ]);
        $program = Program::create([
            'code' => "KAIP{$suffix}",
            'name' => "KAI Program {$suffix}",
            'duration_years' => 4,
            'status' => 'active',
        ]);
        $major = Major::create([
            'department_id' => $department->id,
            'program_id' => $program->id,
            'code' => "KAIM{$suffix}",
            'name' => "KAI Major {$suffix}",
            'status' => 'active',
        ]);
        $academicYear = AcademicYear::create([
            'name' => "2026-2027 KAI {$suffix}",
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'status' => 'active',
        ]);
        $classSection = ClassSection::create([
            'academic_year_id' => $academicYear->id,
            'major_id' => $major->id,
            'name' => "KAI Class {$suffix}",
            'year_level' => 1,
            'section' => $suffix,
            'status' => 'active',
        ]);

        StudentProfile::create([
            'user_id' => $user->id,
            'student_no' => "STU-{$suffix}",
            'roll_no' => "STU-{$suffix}",
            'institutional_email' => "student-{$suffix}@school.test",
            'first_name' => 'Student',
            'last_name' => $suffix,
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
