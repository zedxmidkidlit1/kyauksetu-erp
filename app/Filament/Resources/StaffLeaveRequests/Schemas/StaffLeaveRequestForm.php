<?php

namespace App\Filament\Resources\StaffLeaveRequests\Schemas;

use App\Models\StaffProfile;
use App\Models\TeacherProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaffLeaveRequestForm
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
                Section::make('Leave')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('leave_type')
                                    ->options([
                                        'casual' => 'Casual',
                                        'medical' => 'Medical',
                                        'maternity' => 'Maternity',
                                        'study' => 'Study',
                                        'unpaid' => 'Unpaid',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                DatePicker::make('starts_at')
                                    ->required(),
                                DatePicker::make('ends_at')
                                    ->required(),
                                DateTimePicker::make('requested_at'),
                                Select::make('approved_by')
                                    ->relationship('approvedBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('approved_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
