<?php

namespace App\Filament\Resources\StudentPayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentProfile.student_no')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentFee.feeType.name')
                    ->label('Student fee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('receivedBy.email')
                    ->label('Received by')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('studentProfile')
                    ->relationship('studentProfile', 'student_no')
                    ->label('Student')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('studentFee')
                    ->relationship('studentFee', 'id')
                    ->label('Student fee')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank transfer',
                        'mobile_payment' => 'Mobile payment',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('receivedBy')
                    ->relationship('receivedBy', 'email')
                    ->label('Received by')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
