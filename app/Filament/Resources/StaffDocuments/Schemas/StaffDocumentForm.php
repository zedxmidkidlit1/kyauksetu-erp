<?php

namespace App\Filament\Resources\StaffDocuments\Schemas;

use App\Models\StaffProfile;
use App\Models\TeacherProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StaffDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff member')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('staff_profile_id')
                                    ->relationship('staffProfile', 'staff_no')
                                    ->getOptionLabelFromRecordUsing(fn (StaffProfile $record): string => $record->staff_no ?? "Staff #{$record->id}")
                                    ->searchable()
                                    ->preload(),
                                Select::make('teacher_profile_id')
                                    ->relationship('teacherProfile', 'staff_no')
                                    ->getOptionLabelFromRecordUsing(fn (TeacherProfile $record): string => $record->staff_no ?? "Teacher #{$record->id}")
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Document')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('document_type')
                                    ->options([
                                        'contract' => 'Contract',
                                        'certificate' => 'Certificate',
                                        'appointment_letter' => 'Appointment letter',
                                        'id_document' => 'ID document',
                                        'other' => 'Other',
                                    ])
                                    ->required(),
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('file_path')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                DatePicker::make('issued_at'),
                                DatePicker::make('expires_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
