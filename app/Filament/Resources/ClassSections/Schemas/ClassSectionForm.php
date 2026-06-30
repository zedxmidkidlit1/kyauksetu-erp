<?php

namespace App\Filament\Resources\ClassSections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Class section')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('year_level')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->required(),
                                TextInput::make('section')
                                    ->required()
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
            ]);
    }
}
