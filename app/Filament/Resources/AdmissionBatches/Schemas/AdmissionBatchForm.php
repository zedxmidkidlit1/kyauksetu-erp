<?php

namespace App\Filament\Resources\AdmissionBatches\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdmissionBatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Batch')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'open' => 'Open',
                                        'closed' => 'Closed',
                                        'archived' => 'Archived',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                TextInput::make('capacity')
                                    ->numeric()
                                    ->minValue(0),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Dates and notes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('opens_at'),
                                DatePicker::make('closes_at'),
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
