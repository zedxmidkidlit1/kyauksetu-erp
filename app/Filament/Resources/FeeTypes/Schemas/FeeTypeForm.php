<?php

namespace App\Filament\Resources\FeeTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FeeTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fee type')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('fee_category')
                                    ->options([
                                        'tuition' => 'Tuition',
                                        'registration' => 'Registration',
                                        'exam' => 'Exam',
                                        'hostel' => 'Hostel',
                                        'library' => 'Library',
                                        'laboratory' => 'Laboratory',
                                        'fine' => 'Fine',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('active')
                                    ->required(),
                                TextInput::make('amount')
                                    ->numeric()
                                    ->minValue(0),
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
