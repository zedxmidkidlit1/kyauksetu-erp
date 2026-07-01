<?php

namespace App\Filament\Resources\AdmissionDecisions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdmissionDecisionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admissionApplication.application_no')
                    ->label('Application no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('admissionApplication.applicant.applicant_no')
                    ->label('Applicant no'),
                TextColumn::make('decision_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('decidedBy.email')
                    ->label('Decided by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('decided_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('offer_expires_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('decision_status')
                    ->options([
                        'pending' => 'Pending',
                        'offered' => 'Offered',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'waitlisted' => 'Waitlisted',
                        'withdrawn' => 'Withdrawn',
                    ]),
                SelectFilter::make('decidedBy')
                    ->relationship('decidedBy', 'email')
                    ->label('Decided by')
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
