<?php

namespace App\Filament\Resources\AssessmentComponents\RelationManagers;

use App\Models\StudentEnrollment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

class StudentMarksRelationManager extends RelationManager
{
    protected static string $relationship = 'studentMarks';

    protected static ?string $title = 'Student marks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student mark')
                    ->schema([
                        Grid::make(2)
                            ->schema([
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
                                TextInput::make('marks_obtained')
                                    ->numeric()
                                    ->minValue(0),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                        'locked' => 'Locked',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Select::make('entered_by')
                                    ->relationship('enteredBy', 'email')
                                    ->label('Entered by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('entered_at')
                                    ->seconds(false),
                                Select::make('approved_by')
                                    ->relationship('approvedBy', 'email')
                                    ->label('Approved by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('approved_at')
                                    ->seconds(false),
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
            ->recordTitleAttribute('student_enrollment_id')
            ->columns([
                TextColumn::make('studentEnrollment.roll_no')
                    ->label('Enrollment')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentEnrollment.studentProfile.student_no')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('marks_obtained')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('enteredBy.email')
                    ->label('Entered by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('entered_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approvedBy.email')
                    ->label('Approved by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'locked' => 'Locked',
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
