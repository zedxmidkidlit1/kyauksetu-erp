<?php

namespace App\Filament\Resources\Curricula\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CurriculumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Curriculum')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('semester_id')
                                    ->relationship('semester', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('year_level')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('active')
                                    ->required(),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Courses')
                    ->schema([
                        Repeater::make('curriculumCourses')
                            ->relationship()
                            ->schema([
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Toggle::make('is_required')
                                    ->default(true)
                                    ->required(),
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(1000),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
