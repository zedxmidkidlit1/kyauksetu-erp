<?php

namespace App\Filament\Resources\Hostels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HostelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hostel')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('gender_type')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                        'mixed' => 'Mixed',
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
                                Textarea::make('description')
                                    ->columnSpanFull(),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
