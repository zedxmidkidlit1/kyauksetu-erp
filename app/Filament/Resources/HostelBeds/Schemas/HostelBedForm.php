<?php

namespace App\Filament\Resources\HostelBeds\Schemas;

use App\Models\HostelRoom;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HostelBedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('hostel_room_id')
                                    ->relationship('hostelRoom', 'room_no')
                                    ->getOptionLabelFromRecordUsing(fn (HostelRoom $record): string => sprintf(
                                        '%s - %s',
                                        $record->hostel?->name ?? "Hostel #{$record->hostel_id}",
                                        $record->room_no,
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('bed_no')
                                    ->label('Bed no.')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('bed_status')
                                    ->options([
                                        'available' => 'Available',
                                        'occupied' => 'Occupied',
                                        'damaged' => 'Damaged',
                                        'maintenance' => 'Maintenance',
                                        'retired' => 'Retired',
                                    ])
                                    ->default('available')
                                    ->required(),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
