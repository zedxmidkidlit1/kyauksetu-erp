<?php

namespace App\Filament\Resources\StaffLeaveRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StaffLeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('staffProfile.staff_no')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacherProfile.staff_no')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('leave_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('approvedBy.email')
                    ->label('Approved by')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('leave_type')
                    ->options([
                        'casual' => 'Casual',
                        'medical' => 'Medical',
                        'maternity' => 'Maternity',
                        'study' => 'Study',
                        'unpaid' => 'Unpaid',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('approvedBy')
                    ->relationship('approvedBy', 'email')
                    ->label('Approved by')
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
