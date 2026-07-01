<?php

namespace App\Filament\Resources\HostelRooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HostelRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Room')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('hostel_id')
                                    ->relationship('hostel', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('room_no')
                                    ->label('Room no.')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('floor')
                                    ->maxLength(255),
                                TextInput::make('capacity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10000),
                                Select::make('room_type')
                                    ->options([
                                        'standard' => 'Standard',
                                        'shared' => 'Shared',
                                        'single' => 'Single',
                                        'dormitory' => 'Dormitory',
                                        'other' => 'Other',
                                    ]),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'maintenance' => 'Maintenance',
                                        'closed' => 'Closed',
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
