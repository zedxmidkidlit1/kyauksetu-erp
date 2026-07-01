<?php

namespace App\Filament\Resources\HostelAllocations\Schemas;

use App\Models\HostelBed;
use App\Models\HostelRoom;
use App\Models\StudentProfile;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HostelAllocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Allocation')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_profile_id')
                                    ->relationship('studentProfile', 'student_no')
                                    ->getOptionLabelFromRecordUsing(fn (StudentProfile $record): string => $record->student_no ?? "Student #{$record->id}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('allocation_status')
                                    ->options([
                                        'active' => 'Active',
                                        'vacated' => 'Vacated',
                                        'cancelled' => 'Cancelled',
                                        'transferred' => 'Transferred',
                                    ])
                                    ->default('active')
                                    ->required(),
                                DateTimePicker::make('allocated_at')
                                    ->default(now())
                                    ->required(),
                                DateTimePicker::make('vacated_at'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Hostel placement')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('hostel_id')
                                    ->relationship('hostel', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('hostel_room_id')
                                    ->relationship('hostelRoom', 'room_no')
                                    ->getOptionLabelFromRecordUsing(fn (HostelRoom $record): string => sprintf(
                                        '%s - %s',
                                        $record->hostel?->name ?? "Hostel #{$record->hostel_id}",
                                        $record->room_no,
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('hostel_bed_id')
                                    ->relationship('hostelBed', 'bed_no')
                                    ->getOptionLabelFromRecordUsing(fn (HostelBed $record): string => sprintf(
                                        '%s - %s',
                                        $record->hostelRoom?->room_no ?? "Room #{$record->hostel_room_id}",
                                        $record->bed_no,
                                    ))
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Processing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('allocated_by')
                                    ->relationship('allocatedBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                Select::make('vacated_by')
                                    ->relationship('vacatedBy', 'email')
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
