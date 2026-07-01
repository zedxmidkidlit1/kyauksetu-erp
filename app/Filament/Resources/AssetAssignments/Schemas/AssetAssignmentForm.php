<?php

namespace App\Filament\Resources\AssetAssignments\Schemas;

use App\Models\Asset;
use App\Models\Room;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assignment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('asset_id')
                                    ->relationship('asset', 'asset_tag')
                                    ->getOptionLabelFromRecordUsing(fn (Asset $record): string => "{$record->asset_tag} - {$record->name}")
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('assignment_status')
                                    ->options([
                                        'active' => 'Active',
                                        'returned' => 'Returned',
                                        'transferred' => 'Transferred',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('active')
                                    ->required(),
                                DateTimePicker::make('assigned_at')
                                    ->default(now())
                                    ->required(),
                                DateTimePicker::make('returned_at'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Assigned to')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('assigned_to_user_id')
                                    ->relationship('assignedToUser', 'email')
                                    ->searchable()
                                    ->preload(),
                                Select::make('assigned_to_department_id')
                                    ->relationship('assignedToDepartment', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('assigned_to_room_id')
                                    ->relationship('assignedToRoom', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Room $record): string => trim(($record->building?->name ? "{$record->building->name} - " : '').$record->name))
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Processing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('assigned_by')
                                    ->relationship('assignedBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                Select::make('returned_by')
                                    ->relationship('returnedBy', 'email')
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
