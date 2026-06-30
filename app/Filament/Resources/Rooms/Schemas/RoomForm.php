<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Room')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('building_id')
                                    ->relationship('building', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->maxLength(255),
                                Select::make('room_type')
                                    ->options([
                                        'classroom' => 'Classroom',
                                        'laboratory' => 'Laboratory',
                                        'lecture_hall' => 'Lecture hall',
                                        'office' => 'Office',
                                        'library' => 'Library',
                                        'workshop' => 'Workshop',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                TextInput::make('capacity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10000),
                                TextInput::make('floor')
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'maintenance' => 'Maintenance',
                                    ])
                                    ->default('active')
                                    ->required(),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
