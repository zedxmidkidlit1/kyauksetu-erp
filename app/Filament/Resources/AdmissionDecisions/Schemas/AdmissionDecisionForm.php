<?php

namespace App\Filament\Resources\AdmissionDecisions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdmissionDecisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Decision')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('admission_application_id')
                                    ->relationship('admissionApplication', 'application_no')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('decision_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'offered' => 'Offered',
                                        'accepted' => 'Accepted',
                                        'rejected' => 'Rejected',
                                        'waitlisted' => 'Waitlisted',
                                        'withdrawn' => 'Withdrawn',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                Select::make('decided_by')
                                    ->relationship('decidedBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('decided_at'),
                                DateTimePicker::make('offer_expires_at'),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
