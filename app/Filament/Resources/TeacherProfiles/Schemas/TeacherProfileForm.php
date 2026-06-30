<?php

namespace App\Filament\Resources\TeacherProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teacher profile')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('staff_no')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('institutional_email')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('position')
                                    ->maxLength(255),
                                TextInput::make('rank')
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
