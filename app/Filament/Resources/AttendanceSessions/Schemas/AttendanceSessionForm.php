<?php

namespace App\Filament\Resources\AttendanceSessions\Schemas;

use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance session')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('session_date')
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'open' => 'Open',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('semester_id')
                                    ->relationship('semester', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('class_section_id')
                                    ->relationship('classSection', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Assignment and schedule')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('teaching_assignment_id')
                                    ->relationship('teachingAssignment', 'id')
                                    ->label('Teaching assignment')
                                    ->getOptionLabelFromRecordUsing(fn (TeachingAssignment $record): string => sprintf(
                                        '%s - %s',
                                        $record->teacherProfile?->staff_no ?? "Teacher #{$record->teacher_profile_id}",
                                        $record->course?->code ?? $record->course?->name ?? "Course #{$record->course_id}",
                                    ))
                                    ->searchable()
                                    ->preload(),
                                Select::make('timetable_slot_id')
                                    ->relationship('timetableSlot', 'day_of_week')
                                    ->label('Timetable slot')
                                    ->getOptionLabelFromRecordUsing(fn (TimetableSlot $record): string => sprintf(
                                        '%s %s-%s',
                                        ucfirst($record->day_of_week),
                                        $record->starts_at,
                                        $record->ends_at,
                                    ))
                                    ->searchable()
                                    ->preload(),
                                Select::make('teacher_profile_id')
                                    ->relationship('teacherProfile', 'staff_no')
                                    ->label('Teacher')
                                    ->searchable()
                                    ->preload(),
                                Select::make('room_id')
                                    ->relationship('room', 'name')
                                    ->searchable()
                                    ->preload(),
                                TimePicker::make('starts_at')
                                    ->seconds(false),
                                TimePicker::make('ends_at')
                                    ->seconds(false),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
