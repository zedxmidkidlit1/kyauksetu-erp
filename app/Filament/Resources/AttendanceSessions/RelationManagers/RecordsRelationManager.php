<?php

namespace App\Filament\Resources\AttendanceSessions\RelationManagers;

use App\Models\StudentEnrollment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'records';

    protected static ?string $title = 'Records';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance record')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_enrollment_id')
                                    ->relationship('studentEnrollment', 'roll_no')
                                    ->label('Student enrollment')
                                    ->getOptionLabelFromRecordUsing(fn (StudentEnrollment $record): string => sprintf(
                                        '%s - %s',
                                        $record->roll_no ?? "Enrollment #{$record->id}",
                                        $record->studentProfile?->student_no ?? "Student #{$record->student_profile_id}",
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'present' => 'Present',
                                        'absent' => 'Absent',
                                        'late' => 'Late',
                                        'excused' => 'Excused',
                                        'leave' => 'Leave',
                                    ])
                                    ->default('present')
                                    ->required(),
                                DateTimePicker::make('marked_at')
                                    ->seconds(false),
                                Select::make('marked_by')
                                    ->relationship('markedBy', 'email')
                                    ->label('Marked by')
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
            ->recordTitleAttribute('status')
            ->columns([
                TextColumn::make('studentEnrollment.roll_no')
                    ->label('Enrollment')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentEnrollment.studentProfile.student_no')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('marked_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('markedBy.email')
                    ->label('Marked by')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'excused' => 'Excused',
                        'leave' => 'Leave',
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
