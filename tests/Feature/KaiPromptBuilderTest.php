<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Kai\KaiPromptBuilder;
use Tests\TestCase;

class KaiPromptBuilderTest extends TestCase
{
    public function test_prompt_builder_includes_student_safe_context(): void
    {
        config(['kai.context_limits.timetable_items' => 1]);

        $prompt = app(KaiPromptBuilder::class)->build(
            User::factory()->make(['name' => 'Own Student']),
            'Show my timetable',
            $this->studentContext(),
        );

        $this->assertSame('Show my timetable', $prompt['user_message']);
        $this->assertSame('Own Student', data_get($prompt, 'context_summary.user.name'));
        $this->assertSame('STU-OWN', data_get($prompt, 'context_summary.student_profile.student_no'));
        $this->assertSame('Program OWN', data_get($prompt, 'context_summary.student_profile.program.name'));
        $this->assertSame('Course OWN', data_get($prompt, 'context_summary.today_upcoming_timetable.items.0.course.name'));
        $this->assertCount(1, data_get($prompt, 'context_summary.today_upcoming_timetable.items'));
        $this->assertContains('Answer only using the allowed student context in this prompt.', $prompt['safety_rules']);
    }

    public function test_prompt_builder_excludes_another_students_data(): void
    {
        $context = $this->studentContext();
        $context['other_student'] = [
            'student_no' => 'STU-OTHER',
            'name' => 'Other Student',
        ];
        $context['student_profile']['other_student'] = [
            'student_no' => 'STU-NESTED-OTHER',
        ];

        $prompt = app(KaiPromptBuilder::class)->build(
            User::factory()->make(['name' => 'Own Student']),
            'Who else is in my class?',
            $context,
        );

        $json = json_encode($prompt, JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('STU-OWN', $json);
        $this->assertStringNotContainsString('STU-OTHER', $json);
        $this->assertStringNotContainsString('STU-NESTED-OTHER', $json);
        $this->assertStringNotContainsString('Other Student', $json);
    }

    public function test_prompt_builder_does_not_expose_raw_permissions_secrets_or_internal_ids(): void
    {
        $context = $this->studentContext();
        $context['permissions'] = ['students.viewAny', 'fees.update'];
        $context['api_key'] = 'secret-provider-key';
        $context['student_profile']['program']['secret_token'] = 'program-secret';
        $context['today_upcoming_timetable']['items'][0]['permission_name'] = 'raw-permission-data';

        $prompt = app(KaiPromptBuilder::class)->build(
            User::factory()->make(['name' => 'Own Student']),
            'Show my ERP data',
            $context,
        );

        $json = json_encode($prompt, JSON_THROW_ON_ERROR);

        $this->assertStringNotContainsString('"id"', $json);
        $this->assertStringNotContainsString('student_profile_id', $json);
        $this->assertStringNotContainsString('students.viewAny', $json);
        $this->assertStringNotContainsString('secret-provider-key', $json);
        $this->assertStringNotContainsString('program-secret', $json);
        $this->assertStringNotContainsString('raw-permission-data', $json);
    }

    /**
     * @return array<string, mixed>
     */
    private function studentContext(): array
    {
        return [
            'generated_at' => '2026-07-02T08:00:00Z',
            'user' => [
                'id' => 10,
                'name' => 'Own Student',
                'email' => 'own@example.test',
                'roles' => ['student'],
            ],
            'student_profile' => [
                'id' => 20,
                'student_no' => 'STU-OWN',
                'roll_no' => 'ROLL-OWN',
                'name' => 'Own Student',
                'institutional_email' => 'own@school.test',
                'status' => 'active',
                'program' => [
                    'id' => 30,
                    'name' => 'Program OWN',
                    'code' => 'P-OWN',
                ],
                'major' => [
                    'id' => 40,
                    'name' => 'Major OWN',
                    'code' => 'M-OWN',
                ],
                'class_section' => [
                    'id' => 50,
                    'name' => 'Class OWN',
                    'section' => 'A',
                ],
            ],
            'current_enrollment' => [
                'id' => 60,
                'student_profile_id' => 20,
                'roll_no' => 'ROLL-OWN',
                'year_level' => 1,
                'status' => 'active',
                'academic_year' => [
                    'id' => 70,
                    'name' => '2026-2027',
                ],
            ],
            'today_upcoming_timetable' => [
                'today' => 'Thursday',
                'items' => [
                    [
                        'timetable_id' => 80,
                        'starts_at' => '09:00',
                        'ends_at' => '10:00',
                        'course' => [
                            'id' => 90,
                            'name' => 'Course OWN',
                            'code' => 'COURSE-OWN',
                        ],
                    ],
                    [
                        'starts_at' => '11:00',
                        'ends_at' => '12:00',
                        'course' => [
                            'name' => 'Course EXTRA',
                        ],
                    ],
                ],
            ],
            'visible_announcements' => [
                'count' => 1,
                'items' => [
                    [
                        'id' => 100,
                        'title' => 'Announcement OWN',
                        'priority' => 'normal',
                    ],
                ],
            ],
            'attendance' => [
                'total_recent' => 1,
                'present_count' => 1,
                'absent_count' => 0,
                'latest' => [
                    [
                        'id' => 110,
                        'status' => 'present',
                        'course' => ['name' => 'Course OWN'],
                    ],
                ],
            ],
            'latest_results' => [
                'count' => 1,
                'items' => [
                    [
                        'id' => 120,
                        'grade' => 'A',
                        'course' => ['name' => 'Course OWN'],
                    ],
                ],
            ],
            'unpaid_due_fees' => [
                'count' => 1,
                'total_payable_amount' => '100000.00',
                'items' => [
                    [
                        'id' => 130,
                        'payable_amount' => '100000.00',
                        'fee_status' => 'due',
                    ],
                ],
            ],
            'active_library_loans' => [
                'count' => 1,
                'items' => [
                    [
                        'id' => 140,
                        'book' => ['title' => 'Book OWN'],
                    ],
                ],
            ],
            'active_hostel_allocation' => [
                'id' => 150,
                'hostel' => ['name' => 'Hostel OWN'],
                'room' => ['room_no' => '101'],
            ],
        ];
    }
}
