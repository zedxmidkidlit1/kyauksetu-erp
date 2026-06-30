<?php

namespace App\Filament\Resources\StudentCourseResults\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentCourseResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentEnrollment.roll_no')
                    ->label('Enrollment')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('studentEnrollment.studentProfile.student_no')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->sortable(),
                TextColumn::make('semester.name')
                    ->sortable(),
                TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_marks')
                    ->sortable(),
                TextColumn::make('percentage')
                    ->sortable(),
                TextColumn::make('grade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grade_point')
                    ->sortable(),
                TextColumn::make('result_status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('academicYear')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('gradeScale')
                    ->relationship('gradeScale', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('result_status')
                    ->options([
                        'draft' => 'Draft',
                        'calculated' => 'Calculated',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'locked' => 'Locked',
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
