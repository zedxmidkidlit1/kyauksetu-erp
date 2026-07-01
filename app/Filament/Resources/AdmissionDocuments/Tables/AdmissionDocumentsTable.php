<?php

namespace App\Filament\Resources\AdmissionDocuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdmissionDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admissionApplication.application_no')
                    ->label('Application no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant.applicant_no')
                    ->label('Applicant no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('document_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('document_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('verifiedBy.email')
                    ->label('Verified by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('document_type')
                    ->options([
                        'id_document' => 'ID document',
                        'transcript' => 'Transcript',
                        'certificate' => 'Certificate',
                        'photo' => 'Photo',
                        'recommendation' => 'Recommendation',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('document_status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                        'waived' => 'Waived',
                    ]),
                SelectFilter::make('verifiedBy')
                    ->relationship('verifiedBy', 'email')
                    ->label('Verified by')
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
