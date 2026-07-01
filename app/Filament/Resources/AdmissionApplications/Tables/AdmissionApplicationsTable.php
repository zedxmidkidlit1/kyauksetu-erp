<?php

namespace App\Filament\Resources\AdmissionApplications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdmissionApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application_no')
                    ->label('Application no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('admissionBatch.name')
                    ->label('Batch')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant.applicant_no')
                    ->label('Applicant no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant.last_name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('program.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('major.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('application_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('applied_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('admissionBatch')
                    ->relationship('admissionBatch', 'name')
                    ->label('Batch')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('academicYear')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('program')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('major')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('application_status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under review',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'waitlisted' => 'Waitlisted',
                        'cancelled' => 'Cancelled',
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
