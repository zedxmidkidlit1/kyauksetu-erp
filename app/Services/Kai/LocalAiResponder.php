<?php

namespace App\Services\Kai;

use App\Services\Kai\Contracts\AiResponder;
use Illuminate\Support\Str;

class LocalAiResponder implements AiResponder
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context): array
    {
        $normalizedMessage = Str::of($message)->lower();

        return [
            'reply' => $this->replyFor($normalizedMessage, $context),
            'suggestions' => [
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
}
