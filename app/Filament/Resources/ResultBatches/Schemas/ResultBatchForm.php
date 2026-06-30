<?php

namespace App\Filament\Resources\ResultBatches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResultBatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Result batch')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'prepared' => 'Prepared',
                                        'reviewed' => 'Reviewed',
                                        'approved' => 'Approved',
                                        'published' => 'Published',
                                        'locked' => 'Locked',
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
                                Select::make('program_id')
                                    ->relationship('program', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('major_id')
                                    ->relationship('major', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('class_section_id')
                                    ->relationship('classSection', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('exam_term_id')
                                    ->relationship('examTerm', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Workflow')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('prepared_by')
                                    ->relationship('preparedBy', 'email')
                                    ->label('Prepared by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('prepared_at')
                                    ->seconds(false),
                                Select::make('reviewed_by')
                                    ->relationship('reviewedBy', 'email')
                                    ->label('Reviewed by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('reviewed_at')
                                    ->seconds(false),
                                Select::make('approved_by')
                                    ->relationship('approvedBy', 'email')
                                    ->label('Approved by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('approved_at')
                                    ->seconds(false),
                                DateTimePicker::make('published_at')
                                    ->seconds(false),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
