<?php

namespace App\Filament\Resources\ResultBatches\RelationManagers;

use App\Models\StudentCourseResult;
use App\Models\StudentEnrollment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Result batch item')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_course_result_id')
                                    ->relationship('studentCourseResult', 'id')
                                    ->label('Student course result')
                                    ->getOptionLabelFromRecordUsing(fn (StudentCourseResult $record): string => sprintf(
                                        '%s - %s - %s',
                                        $record->studentEnrollment?->roll_no ?? "Enrollment #{$record->student_enrollment_id}",
                                        $record->course?->code ?? $record->course?->name ?? "Course #{$record->course_id}",
                                        $record->grade ?? $record->result_status,
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('student_enrollment_id')
                                    ->relationship('studentEnrollment', 'roll_no')
                                    ->label('Student enrollment')
                                    ->getOptionLabelFromRecordUsing(fn (StudentEnrollment $record): string => sprintf(
                                        '%s - %s',
                                        $record->roll_no,
                                        $record->studentProfile?->student_no ?? "Student #{$record->student_profile_id}",
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'included' => 'Included',
                                        'withheld' => 'Withheld',
                                        'corrected' => 'Corrected',
                                        'removed' => 'Removed',
                                    ])
                                    ->default('included')
                                    ->required(),
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
            ->recordTitleAttribute('student_course_result_id')
            ->columns([
                TextColumn::make('studentEnrollment.roll_no')
                    ->label('Enrollment')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentEnrollment.studentProfile.student_no')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentCourseResult.id')
                    ->label('Course result')
                    ->sortable(),
                TextColumn::make('studentCourseResult.grade')
                    ->label('Grade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('remarks')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'included' => 'Included',
                        'withheld' => 'Withheld',
                        'corrected' => 'Corrected',
                        'removed' => 'Removed',
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
