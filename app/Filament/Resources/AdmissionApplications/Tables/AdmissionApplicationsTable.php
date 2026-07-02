<?php

namespace App\Filament\Resources\AdmissionApplications\Tables;

use App\Models\AdmissionApplication;
use App\Models\StudentEnrollment;
use App\Models\StudentProfile;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use RuntimeException;

class AdmissionApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application_no')
                    ->label('Application no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('admissionBatch.name')
                    ->label('Batch')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant.applicant_no')
                    ->label('Applicant no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant.last_name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('program.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('major.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('application_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('studentProfile.student_no')
                    ->label('Student no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('converted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('applied_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('admissionBatch')
                    ->relationship('admissionBatch', 'name')
                    ->label('Batch')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('academicYear')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('program')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('major')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('application_status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under review',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'waitlisted' => 'Waitlisted',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                Action::make('convertToStudent')
                    ->label('Convert')
                    ->icon(Heroicon::UserPlus)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convert applicant to student')
                    ->modalDescription('This creates a student profile and student enrollment from this accepted application.')
                    ->modalSubmitActionLabel('Convert')
                    ->visible(function (AdmissionApplication $record): bool {
                        $user = auth()->user();

                        return $user !== null
                            && $user->can('update', $record)
                            && $user->can('create', StudentProfile::class)
                            && $user->can('create', StudentEnrollment::class)
                            && $record->isAcceptedForConversion()
                            && ! $record->isConverted();
                    })
                    ->action(function (AdmissionApplication $record): void {
                        $user = auth()->user();

                        if (! $user) {
                            Notification::make()
                                ->title('Conversion failed')
                                ->body('An authenticated admin user is required.')
                                ->danger()
                                ->send();

                            return;
                        }

                        try {
                            $studentProfile = $record->convertToStudent($user);
                        } catch (RuntimeException $exception) {
                            Notification::make()
                                ->title('Conversion failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Applicant converted')
                            ->body("Student profile {$studentProfile->student_no} was created.")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
