<?php

namespace App\Filament\Resources\StudentProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student_no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roll_no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('institutional_email')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->sortable(),
                TextColumn::make('program.name')
                    ->sortable(),
                TextColumn::make('major.name')
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->sortable(),
                TextColumn::make('classSection.name')
                    ->sortable(),
                TextColumn::make('admission_year')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Login email')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
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
                SelectFilter::make('academicYear')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('classSection')
                    ->relationship('classSection', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
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
