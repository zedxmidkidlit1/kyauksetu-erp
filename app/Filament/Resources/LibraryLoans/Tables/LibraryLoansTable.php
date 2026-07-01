<?php

namespace App\Filament\Resources\LibraryLoans\Tables;

use App\Models\LibraryLoan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LibraryLoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bookCopy.accession_no')
                    ->label('Book copy')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('borrower')
                    ->state(fn (LibraryLoan $record): string => static::borrowerLabel($record)),
                TextColumn::make('borrowed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('loan_status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('bookCopy')
                    ->relationship('bookCopy', 'accession_no')
                    ->label('Book copy')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('loan_status')
                    ->options([
                        'active' => 'Active',
                        'returned' => 'Returned',
                        'overdue' => 'Overdue',
                        'lost' => 'Lost',
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

    protected static function borrowerLabel(LibraryLoan $record): string
    {
        if ($record->studentProfile !== null) {
            return 'Student: '.($record->studentProfile->student_no ?? "Student #{$record->studentProfile->id}");
        }

        if ($record->staffProfile !== null) {
            return 'Staff: '.($record->staffProfile->staff_no ?? "Staff #{$record->staffProfile->id}");
        }

        if ($record->teacherProfile !== null) {
            return 'Teacher: '.($record->teacherProfile->staff_no ?? "Teacher #{$record->teacherProfile->id}");
        }

        return 'Unassigned';
    }
}
