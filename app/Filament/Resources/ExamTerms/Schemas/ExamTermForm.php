<?php

namespace App\Filament\Resources\ExamTerms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamTermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exam term')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('exam_type')
                                    ->options([
                                        'midterm' => 'Midterm',
                                        'final' => 'Final',
                                        'supplementary' => 'Supplementary',
                                        'practical' => 'Practical',
                                        'quiz' => 'Quiz',
                                        'other' => 'Other',
                                    ])
                                    ->default('midterm')
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
                                DatePicker::make('starts_at'),
                                DatePicker::make('ends_at'),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'scheduled' => 'Scheduled',
                                        'active' => 'Active',
                                        'completed' => 'Completed',
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
