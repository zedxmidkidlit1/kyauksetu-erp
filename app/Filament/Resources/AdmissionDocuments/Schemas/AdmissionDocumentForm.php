<?php

namespace App\Filament\Resources\AdmissionDocuments\Schemas;

use App\Models\AdmissionApplication;
use App\Models\Applicant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdmissionDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Application')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('admission_application_id')
                                    ->relationship('admissionApplication', 'application_no')
                                    ->getOptionLabelFromRecordUsing(fn (AdmissionApplication $record): string => $record->application_no)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('applicant_id')
                                    ->relationship('applicant', 'applicant_no')
                                    ->getOptionLabelFromRecordUsing(fn (Applicant $record): string => trim("{$record->applicant_no} {$record->full_name}") ?: "Applicant #{$record->id}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Document')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('document_type')
                                    ->options([
                                        'id_document' => 'ID document',
                                        'transcript' => 'Transcript',
                                        'certificate' => 'Certificate',
                                        'photo' => 'Photo',
                                        'recommendation' => 'Recommendation',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                Select::make('document_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'received' => 'Received',
                                        'verified' => 'Verified',
                                        'rejected' => 'Rejected',
                                        'waived' => 'Waived',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('file_path')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                DatePicker::make('issued_at'),
                                DatePicker::make('expires_at'),
                                Select::make('verified_by')
                                    ->relationship('verifiedBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('verified_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
