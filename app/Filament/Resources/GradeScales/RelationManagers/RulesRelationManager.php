<?php

namespace App\Filament\Resources\GradeScales\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    protected static ?string $title = 'Rules';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Grade scale rule')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('grade')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('is_passing')
                                    ->options([
                                        true => 'Passing',
                                        false => 'Failing',
                                    ])
                                    ->default(true)
                                    ->required(),
                                TextInput::make('min_marks')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('max_marks')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('grade_point')
                                    ->numeric()
                                    ->minValue(0),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('grade')
            ->columns([
                TextColumn::make('grade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('min_marks')
                    ->sortable(),
                TextColumn::make('max_marks')
                    ->sortable(),
                TextColumn::make('grade_point')
                    ->sortable(),
                IconColumn::make('is_passing')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_passing')
                    ->options([
                        true => 'Passing',
                        false => 'Failing',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
