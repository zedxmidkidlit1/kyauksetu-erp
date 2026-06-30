<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('student_no')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('roll_no')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('institutional_email')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Academic placement')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('class_section_id')
                                    ->relationship('classSection', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('admission_year')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(2200),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
