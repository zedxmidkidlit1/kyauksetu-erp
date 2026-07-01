<?php

namespace App\Filament\Resources\AssetAssignments\Tables;

use App\Models\AssetAssignment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.asset_tag')
                    ->label('Asset')
                    ->description(fn (AssetAssignment $record): ?string => $record->asset?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedToUser.email')
                    ->label('Assigned user')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedToDepartment.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedToRoom.name')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assigned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('assignment_status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('asset')
                    ->relationship('asset', 'asset_tag')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assignedToUser')
                    ->relationship('assignedToUser', 'email')
                    ->label('Assigned user')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assignedToDepartment')
                    ->relationship('assignedToDepartment', 'name')
                    ->label('Department')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assignedToRoom')
                    ->relationship('assignedToRoom', 'name')
                    ->label('Room')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assignment_status')
                    ->options([
                        'active' => 'Active',
                        'returned' => 'Returned',
                        'transferred' => 'Transferred',
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
