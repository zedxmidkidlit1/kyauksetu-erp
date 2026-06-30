<?php

namespace App\Filament\Resources\Timetables\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TimetableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Timetable')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'draft' => 'Draft',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('active')
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
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Effective dates and notes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('effective_from'),
                                DatePicker::make('effective_until'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
