<?php

namespace App\Filament\Resources\StaffEmployments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StaffEmploymentsTable
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
                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('staffPosition.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employment_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('employment_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('joined_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('staffPosition')
                    ->relationship('staffPosition', 'name')
                    ->label('Position')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('employment_type')
                    ->options([
                        'permanent' => 'Permanent',
                        'contract' => 'Contract',
                        'part_time' => 'Part time',
                        'temporary' => 'Temporary',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('employment_status')
                    ->options([
                        'active' => 'Active',
                        'resigned' => 'Resigned',
                        'retired' => 'Retired',
                        'terminated' => 'Terminated',
                        'suspended' => 'Suspended',
                        'transferred' => 'Transferred',
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
