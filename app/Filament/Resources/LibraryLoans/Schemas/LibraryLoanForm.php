<?php

namespace App\Filament\Resources\LibraryLoans\Schemas;

use App\Models\BookCopy;
use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LibraryLoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Loan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('book_copy_id')
                                    ->relationship('bookCopy', 'accession_no')
                                    ->getOptionLabelFromRecordUsing(fn (BookCopy $record): string => sprintf(
                                        '%s - %s',
                                        $record->accession_no,
                                        $record->book?->title ?? "Book #{$record->book_id}",
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('loan_status')
                                    ->options([
                                        'active' => 'Active',
                                        'returned' => 'Returned',
                                        'overdue' => 'Overdue',
                                        'lost' => 'Lost',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('active')
                                    ->required(),
                                DateTimePicker::make('borrowed_at')
                                    ->default(now())
                                    ->required(),
                                DateTimePicker::make('due_at'),
                                DateTimePicker::make('returned_at'),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('Borrower')
                    ->description('Select only one borrower type.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('student_profile_id')
                                    ->relationship('studentProfile', 'student_no')
                                    ->getOptionLabelFromRecordUsing(fn (StudentProfile $record): string => $record->student_no ?? "Student #{$record->id}")
                                    ->searchable()
                                    ->preload(),
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
                Section::make('Processing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('issued_by')
                                    ->relationship('issuedBy', 'email')
                                    ->searchable()
                                    ->preload(),
                                Select::make('returned_by')
                                    ->relationship('returnedBy', 'email')
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
