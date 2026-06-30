<?php

namespace App\Filament\Resources\TeacherProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeacherProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('staff_no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('institutional_email')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->sortable(),
                TextColumn::make('position')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rank')
                    ->searchable()
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
