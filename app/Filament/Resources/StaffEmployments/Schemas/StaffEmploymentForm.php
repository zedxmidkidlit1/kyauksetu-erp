<?php

namespace App\Filament\Resources\StaffEmployments\Schemas;

use App\Models\StaffProfile;
use App\Models\TeacherProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaffEmploymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff member')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('staff_profile_id')
                                    ->relationship('staffProfile', 'staff_no')
                                    ->getOptionLabelFromRecordUsing(fn (StaffProfile $record): string => $record->staff_no ?? "Staff #{$record->id}")
                                    ->searchable()
                                    ->preload(),
                                Select::make('teacher_profile_id')
                                    ->relationship('teacherProfile', 'staff_no')
                                    ->getOptionLabelFromRecordUsing(fn (TeacherProfile $record): string => $record->staff_no ?? "Teacher #{$record->id}")
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Employment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('staff_position_id')
                                    ->relationship('staffPosition', 'name')
                                    ->label('Position')
                                    ->searchable()
                                    ->preload(),
                                Select::make('employment_type')
                                    ->options([
                                        'permanent' => 'Permanent',
                                        'contract' => 'Contract',
                                        'part_time' => 'Part time',
                                        'temporary' => 'Temporary',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                Select::make('employment_status')
                                    ->options([
                                        'active' => 'Active',
                                        'resigned' => 'Resigned',
                                        'retired' => 'Retired',
                                        'terminated' => 'Terminated',
                                        'suspended' => 'Suspended',
                                        'transferred' => 'Transferred',
                                    ])
                                    ->default('active')
                                    ->required(),
                                DatePicker::make('joined_at'),
                                DatePicker::make('ended_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
