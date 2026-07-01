<?php

namespace App\Filament\Resources\StaffDocuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StaffDocumentsTable
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
                TextColumn::make('document_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('document_type')
                    ->options([
                        'contract' => 'Contract',
                        'certificate' => 'Certificate',
                        'appointment_letter' => 'Appointment letter',
                        'id_document' => 'ID document',
                        'other' => 'Other',
                    ]),
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
