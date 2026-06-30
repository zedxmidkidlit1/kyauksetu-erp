<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Course')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('credit_hours')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active')
                                    ->required(),
                                TextInput::make('lecture_hours')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                TextInput::make('tutorial_hours')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                TextInput::make('practical_hours')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                Textarea::make('description')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
