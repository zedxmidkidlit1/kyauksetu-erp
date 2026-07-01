<?php

namespace App\Filament\Resources\Announcements\RelationManagers;

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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AudiencesRelationManager extends RelationManager
{
    protected static string $relationship = 'audiences';

    protected static ?string $title = 'Audiences';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Audience')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('audience_type')
                                    ->options([
                                        'all' => 'All',
                                        'role' => 'Role',
                                        'department' => 'Department',
                                        'program' => 'Program',
                                        'major' => 'Major',
                                        'class_section' => 'Class section',
                                        'user' => 'User',
                                    ])
                                    ->default('all')
                                    ->required(),
                                TextInput::make('role_name')
                                    ->maxLength(255),
                                Select::make('department_id')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('class_section_id')
                                    ->relationship('classSection', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('user_id')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload(),
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
            ->recordTitleAttribute('audience_type')
            ->columns([
                TextColumn::make('audience_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('role_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('program.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('major.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classSection.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('remarks')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('audience_type')
                    ->options([
                        'all' => 'All',
                        'role' => 'Role',
                        'department' => 'Department',
                        'program' => 'Program',
                        'major' => 'Major',
                        'class_section' => 'Class section',
                        'user' => 'User',
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
