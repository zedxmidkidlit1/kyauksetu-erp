<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Book')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('book_category_id')
                                    ->relationship('bookCategory', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('active')
                                    ->required(),
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('subtitle')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('author')
                                    ->maxLength(255),
                                TextInput::make('publisher')
                                    ->maxLength(255),
                                TextInput::make('published_year')
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(9999),
                                TextInput::make('edition')
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
