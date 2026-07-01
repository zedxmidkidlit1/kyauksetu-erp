<?php

namespace App\Filament\Resources\StudentFees\Schemas;

use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentFeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student fee')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_profile_id')
                                    ->relationship('studentProfile', 'student_no')
                                    ->getOptionLabelFromRecordUsing(fn (StudentProfile $record): string => $record->student_no ?? "Student #{$record->id}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('fee_type_id')
                                    ->relationship('feeType', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('student_enrollment_id')
                                    ->relationship('studentEnrollment', 'roll_no')
                                    ->getOptionLabelFromRecordUsing(fn (StudentEnrollment $record): string => $record->roll_no ?? "Enrollment #{$record->id}")
                                    ->searchable()
                                    ->preload(),
                                Select::make('fee_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'partial' => 'Partial',
                                        'paid' => 'Paid',
                                        'waived' => 'Waived',
                                        'overdue' => 'Overdue',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Period and amounts')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('semester_id')
                                    ->relationship('semester', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('discount_amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0),
                                TextInput::make('payable_amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                DatePicker::make('due_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
