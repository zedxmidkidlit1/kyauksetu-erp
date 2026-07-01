<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asset')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('asset_tag')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('serial_number')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Select::make('asset_category_id')
                                    ->relationship('assetCategory', 'name')
                                    ->label('Category')
                                    ->searchable()
                                    ->preload(),
                                Select::make('asset_status')
                                    ->options([
                                        'available' => 'Available',
                                        'assigned' => 'Assigned',
                                        'maintenance' => 'Maintenance',
                                        'damaged' => 'Damaged',
                                        'lost' => 'Lost',
                                        'retired' => 'Retired',
                                    ])
                                    ->default('available')
                                    ->required(),
                                Textarea::make('description')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Location')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('building_id')
                                    ->relationship('building', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('room_id')
                                    ->relationship('room', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Purchase')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('purchase_date'),
                                TextInput::make('purchase_cost')
                                    ->numeric()
                                    ->minValue(0),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
