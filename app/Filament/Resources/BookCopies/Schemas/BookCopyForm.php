<?php

namespace App\Filament\Resources\BookCopies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookCopyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Copy')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('book_id')
                                    ->relationship('book', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('accession_no')
                                    ->label('Accession no.')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('barcode')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('copy_status')
                                    ->options([
                                        'available' => 'Available',
                                        'borrowed' => 'Borrowed',
                                        'lost' => 'Lost',
                                        'damaged' => 'Damaged',
                                        'retired' => 'Retired',
                                    ])
                                    ->default('available')
                                    ->required(),
                                TextInput::make('shelf_location')
                                    ->maxLength(255),
                                DatePicker::make('acquired_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
