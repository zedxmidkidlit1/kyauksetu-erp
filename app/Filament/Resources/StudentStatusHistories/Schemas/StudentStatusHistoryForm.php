<?php

namespace App\Filament\Resources\StudentStatusHistories\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentStatusHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status change')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_profile_id')
                                    ->relationship('studentProfile', 'student_no')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                DatePicker::make('effective_date')
                                    ->required()
                                    ->default(now()),
                                Select::make('old_status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'suspended' => 'Suspended',
                                        'withdrawn' => 'Withdrawn',
                                        'graduated' => 'Graduated',
                                        'transferred' => 'Transferred',
                                    ]),
                                Select::make('new_status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'suspended' => 'Suspended',
                                        'withdrawn' => 'Withdrawn',
                                        'graduated' => 'Graduated',
                                        'transferred' => 'Transferred',
                                    ])
                                    ->required(),
                                TextInput::make('reason')
                                    ->maxLength(255),
                                Select::make('changed_by')
                                    ->relationship('changedBy', 'email')
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
