<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Announcement')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('body')
                                    ->required()
                                    ->rows(8)
                                    ->columnSpanFull(),
                                Select::make('announcement_type')
                                    ->options([
                                        'general' => 'General',
                                        'academic' => 'Academic',
                                        'exam' => 'Exam',
                                        'attendance' => 'Attendance',
                                        'result' => 'Result',
                                        'timetable' => 'Timetable',
                                        'administrative' => 'Administrative',
                                        'emergency' => 'Emergency',
                                        'other' => 'Other',
                                    ])
                                    ->default('general')
                                    ->required(),
                                Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'normal' => 'Normal',
                                        'high' => 'High',
                                        'urgent' => 'Urgent',
                                    ])
                                    ->default('normal')
                                    ->required(),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'scheduled' => 'Scheduled',
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Select::make('created_by')
                                    ->relationship('createdBy', 'email')
                                    ->label('Created by')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('publish_at')
                                    ->seconds(false),
                                DateTimePicker::make('expires_at')
                                    ->seconds(false),
                                Textarea::make('remarks')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
