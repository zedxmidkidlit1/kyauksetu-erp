<?php

namespace App\Filament\Resources\Timetables\RelationManagers;

use App\Models\TeachingAssignment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SlotsRelationManager extends RelationManager
{
    protected static string $relationship = 'slots';

    protected static ?string $title = 'Slots';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slot')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('day_of_week')
                                    ->options([
                                        'monday' => 'Monday',
                                        'tuesday' => 'Tuesday',
                                        'wednesday' => 'Wednesday',
                                        'thursday' => 'Thursday',
                                        'friday' => 'Friday',
                                        'saturday' => 'Saturday',
                                        'sunday' => 'Sunday',
                                    ])
                                    ->required(),
                                Select::make('slot_type')
                                    ->options([
                                        'lecture' => 'Lecture',
                                        'tutorial' => 'Tutorial',
                                        'practical' => 'Practical',
                                        'lab' => 'Lab',
                                        'exam' => 'Exam',
                                        'other' => 'Other',
                                    ])
                                    ->default('lecture')
                                    ->required(),
                                TimePicker::make('starts_at')
                                    ->seconds(false)
                                    ->required(),
                                TimePicker::make('ends_at')
                                    ->seconds(false)
                                    ->after('starts_at')
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'scheduled' => 'Scheduled',
                                        'cancelled' => 'Cancelled',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('scheduled')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Assignment and location')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('teaching_assignment_id')
                                    ->relationship('teachingAssignment', 'id')
                                    ->label('Teaching assignment')
                                    ->getOptionLabelFromRecordUsing(fn (TeachingAssignment $record): string => sprintf(
                                        '%s - %s',
                                        $record->teacherProfile?->staff_no ?? "Teacher #{$record->teacher_profile_id}",
                                        $record->course?->code ?? $record->course?->name ?? "Course #{$record->course_id}",
                                    ))
                                    ->searchable()
                                    ->preload(),
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('teacher_profile_id')
                                    ->relationship('teacherProfile', 'staff_no')
                                    ->label('Teacher')
                                    ->searchable()
                                    ->preload(),
                                Select::make('room_id')
                                    ->relationship('room', 'name')
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
            ->recordTitleAttribute('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacherProfile.staff_no')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slot_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->options([
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                        'sunday' => 'Sunday',
                    ]),
                SelectFilter::make('slot_type')
                    ->options([
                        'lecture' => 'Lecture',
                        'tutorial' => 'Tutorial',
                        'practical' => 'Practical',
                        'lab' => 'Lab',
                        'exam' => 'Exam',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'cancelled' => 'Cancelled',
                        'inactive' => 'Inactive',
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
