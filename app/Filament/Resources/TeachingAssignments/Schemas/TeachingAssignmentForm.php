<?php

namespace App\Filament\Resources\TeachingAssignments\Schemas;

use App\Models\CurriculumCourse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeachingAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teaching assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('teacher_profile_id')
                                    ->relationship('teacherProfile', 'staff_no')
                                    ->label('Teacher')
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
                                Select::make('class_section_id')
                                    ->relationship('classSection', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Academic context')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('curriculum_id')
                                    ->relationship('curriculum', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('curriculum_course_id')
                                    ->relationship('curriculumCourse', 'id')
                                    ->label('Curriculum course')
                                    ->getOptionLabelFromRecordUsing(fn (CurriculumCourse $record): string => sprintf(
                                        '%s - %s',
                                        $record->curriculum?->name ?? 'Curriculum',
                                        $record->course?->code ?? $record->course?->name ?? "Course #{$record->course_id}",
                                    ))
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Dates and notes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('starts_at'),
                                DatePicker::make('ends_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
