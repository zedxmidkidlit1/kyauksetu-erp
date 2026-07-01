<?php

namespace App\Filament\Resources\StudentPayments\Schemas;

use App\Models\StudentFee;
use App\Models\StudentProfile;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_profile_id')
                                    ->relationship('studentProfile', 'student_no')
                                    ->getOptionLabelFromRecordUsing(fn (StudentProfile $record): string => $record->student_no ?? "Student #{$record->id}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('student_fee_id')
                                    ->relationship('studentFee', 'id')
                                    ->getOptionLabelFromRecordUsing(fn (StudentFee $record): string => sprintf(
                                        '%s - %s',
                                        $record->feeType?->name ?? "Fee #{$record->fee_type_id}",
                                        $record->payable_amount,
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                DateTimePicker::make('paid_at')
                                    ->default(now())
                                    ->required(),
                                Select::make('payment_method')
                                    ->options([
                                        'cash' => 'Cash',
                                        'bank_transfer' => 'Bank transfer',
                                        'mobile_payment' => 'Mobile payment',
                                        'other' => 'Other',
                                    ]),
                                Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'confirmed' => 'Confirmed',
                                        'refunded' => 'Refunded',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                TextInput::make('payment_reference')
                                    ->maxLength(255),
                                Select::make('received_by')
                                    ->relationship('receivedBy', 'email')
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
