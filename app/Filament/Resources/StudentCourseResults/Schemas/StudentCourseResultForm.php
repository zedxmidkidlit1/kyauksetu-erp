<?php

namespace App\Filament\Resources\StudentCourseResults\Schemas;

use App\Models\StudentEnrollment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentCourseResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Course result')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_enrollment_id')
                                    ->relationship('studentEnrollment', 'roll_no')
                                    ->label('Student enrollment')
                                    ->getOptionLabelFromRecordUsing(fn (StudentEnrollment $record): string => sprintf(
                                        '%s - %s',
                                        $record->roll_no,
                                        $record->studentProfile?->student_no ?? "Student #{$record->student_profile_id}",
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload()
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
                                Select::make('grade_scale_id')
                                    ->relationship('gradeScale', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('result_status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'calculated' => 'Calculated',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'locked' => 'Locked',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                TextInput::make('total_marks')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('percentage')
                                    ->numeric()
                                    ->minValue(0),
                                TextInput::make('grade')
                                    ->maxLength(255),
                                TextInput::make('grade_point')
                                    ->numeric()
                                    ->minValue(0),
                                Select::make('calculated_by')
                                    ->relationship('calculatedBy', 'email')
                                    ->label('Calculated by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('calculated_at')
                                    ->seconds(false),
                                Select::make('approved_by')
                                    ->relationship('approvedBy', 'email')
                                    ->label('Approved by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('approved_at')
                                    ->seconds(false),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
