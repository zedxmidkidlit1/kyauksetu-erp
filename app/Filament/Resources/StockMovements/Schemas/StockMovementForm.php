<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Movement')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('stock_item_id')
                                    ->relationship('stockItem', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('movement_type')
                                    ->options([
                                        'in' => 'In',
                                        'out' => 'Out',
                                        'adjustment' => 'Adjustment',
                                        'transfer' => 'Transfer',
                                    ])
                                    ->required(),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                DatePicker::make('movement_date')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('reference')
                                    ->maxLength(255),
                                Select::make('handled_by')
                                    ->relationship('handledBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
