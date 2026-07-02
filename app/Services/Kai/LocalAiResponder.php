<?php

namespace App\Services\Kai;

use App\Models\User;
use App\Services\Kai\Contracts\AiResponder;
use Illuminate\Support\Str;

class LocalAiResponder implements AiResponder
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context, ?User $user = null): array
    {
        $normalizedMessage = Str::of($message)->lower()->toString();
        $isTeacherContext = array_key_exists('teacher_profile', $context);

        return [
            'reply' => $this->replyFor($normalizedMessage, $context),
            'suggestions' => $isTeacherContext ? [
                'Show my teaching schedule',
                'Summarize attendance sessions',
                'Show pending marks',
            ] : [
                'Show my timetable',
                'Check unpaid fees',
                'Show latest results',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function replyFor(string $message, array $context): string
    {
        if (array_key_exists('teacher_profile', $context)) {
            return $this->teacherReplyFor($message, $context);
        }

        if (str_contains($message, 'fee') || str_contains($message, 'payment')) {
            $fees = $context['unpaid_due_fees'] ?? [];

            return sprintf(
                'You have %d unpaid or due fee item(s) totaling %s.',
                $fees['count'] ?? 0,
                $fees['total_payable_amount'] ?? '0.00',
            );
        }

        if (str_contains($message, 'timetable') || str_contains($message, 'schedule') || str_contains($message, 'class')) {
            $items = $context['today_upcoming_timetable']['items'] ?? [];

            return sprintf('I found %d upcoming timetable item(s) in your student context.', count($items));
        }

        if (str_contains($message, 'result') || str_contains($message, 'grade')) {
            $results = $context['latest_results'] ?? [];

            return sprintf('I found %d latest result item(s) in your student context.', $results['count'] ?? 0);
        }

        if (str_contains($message, 'attendance')) {
            $attendance = $context['attendance'] ?? [];

            return sprintf(
                'Your recent attendance summary has %d present and %d absent record(s).',
                $attendance['present_count'] ?? 0,
                $attendance['absent_count'] ?? 0,
            );
        }

        if (str_contains($message, 'library') || str_contains($message, 'book')) {
            $loans = $context['active_library_loans'] ?? [];

            return sprintf('You have %d active library loan(s).', $loans['count'] ?? 0);
        }

        if (str_contains($message, 'hostel') || str_contains($message, 'room')) {
            return ($context['active_hostel_allocation'] ?? null)
                ? 'You have an active hostel allocation in your student context.'
                : 'I could not find an active hostel allocation in your student context.';
        }

        if (str_contains($message, 'announcement') || str_contains($message, 'notice')) {
            $announcements = $context['visible_announcements'] ?? [];

            return sprintf('You have %d visible announcement(s).', $announcements['count'] ?? 0);
        }

        return 'I can help with your timetable, fees, results, attendance, library, hostel, and announcements using your student context.';
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function teacherReplyFor(string $message, array $context): string
    {
        if (str_contains($message, 'timetable') || str_contains($message, 'schedule') || str_contains($message, 'class')) {
            $items = $context['today_upcoming_timetable']['items'] ?? [];

            return sprintf('I found %d upcoming timetable item(s) in your teacher context.', count($items));
        }

        if (str_contains($message, 'attendance')) {
            $sessions = $context['recent_attendance_sessions'] ?? [];

            return sprintf('I found %d recent attendance session(s) in your teacher context.', $sessions['count'] ?? 0);
        }

        if (str_contains($message, 'mark') || str_contains($message, 'assessment') || str_contains($message, 'result')) {
            $components = $context['assessment_components_pending_marks'] ?? [];

            return sprintf(
                'I found %d assessment component(s) with %d pending mark item(s) in your teacher context.',
                $components['count'] ?? 0,
                $components['pending_count'] ?? 0,
            );
        }

        if (str_contains($message, 'announcement') || str_contains($message, 'notice')) {
            $announcements = $context['visible_announcements'] ?? [];

            return sprintf('You have %d visible announcement(s).', $announcements['count'] ?? 0);
        }

        $assignments = $context['teaching_assignments'] ?? [];
        $classes = $context['assigned_classes'] ?? [];

        return sprintf(
            'I can help with your teaching schedule, assigned classes, attendance sessions, assessment components, and announcements using your teacher context. You have %d assignment(s) and %d assigned class(es).',
            $assignments['count'] ?? 0,
            $classes['count'] ?? 0,
        );
    }
}
