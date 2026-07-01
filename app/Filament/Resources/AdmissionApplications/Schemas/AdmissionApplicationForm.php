<?php

namespace App\Filament\Resources\AdmissionApplications\Schemas;

use App\Models\Applicant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdmissionApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('application_no')
                                    ->label('Application no')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('application_status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'submitted' => 'Submitted',
                                        'under_review' => 'Under review',
                                        'accepted' => 'Accepted',
                                        'rejected' => 'Rejected',
                                        'waitlisted' => 'Waitlisted',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Select::make('admission_batch_id')
                                    ->relationship('admissionBatch', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('applicant_id')
                                    ->relationship('applicant', 'applicant_no')
                                    ->getOptionLabelFromRecordUsing(fn (Applicant $record): string => trim("{$record->applicant_no} {$record->full_name}") ?: "Applicant #{$record->id}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                DateTimePicker::make('applied_at'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Academic preference')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('academic_year_id')
                                    ->relationship('academicYear', 'name')
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
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
