<?php

namespace App\Filament\Resources\AssessmentComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssessmentComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assessment component')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('component_type')
                                    ->options([
                                        'assignment' => 'Assignment',
                                        'quiz' => 'Quiz',
                                        'midterm' => 'Midterm',
                                        'final' => 'Final',
                                        'practical' => 'Practical',
                                        'project' => 'Project',
                                        'attendance' => 'Attendance',
                                        'other' => 'Other',
                                    ])
                                    ->default('assignment')
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
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('exam_term_id')
                                    ->relationship('examTerm', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('exam_schedule_id')
                                    ->relationship('examSchedule', 'id')
                                    ->label('Exam schedule')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('max_marks')
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->required(),
                                TextInput::make('weight')
                                    ->numeric()
                                    ->minValue(0),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'active' => 'Active',
                                        'locked' => 'Locked',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
