<?php

namespace App\Filament\Resources\ExamTerms\RelationManagers;

use App\Models\TeachingAssignment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Schedules';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Exam schedule')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('exam_date')
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'scheduled' => 'Scheduled',
                                        'completed' => 'Completed',
                                        'postponed' => 'Postponed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('semester_id')
                                    ->relationship('semester', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('class_section_id')
                                    ->relationship('classSection', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TimePicker::make('starts_at')
                                    ->seconds(false)
                                    ->required(),
                                TimePicker::make('ends_at')
                                    ->seconds(false)
                                    ->after('starts_at')
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
            ->recordTitleAttribute('exam_date')
            ->columns([
                TextColumn::make('exam_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('classSection.name')
                    ->searchable()
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
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('classSection')
                    ->relationship('classSection', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('teacherProfile')
                    ->relationship('teacherProfile', 'staff_no')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('room')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'postponed' => 'Postponed',
                        'cancelled' => 'Cancelled',
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
