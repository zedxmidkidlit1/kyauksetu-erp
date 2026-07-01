<?php

namespace App\Filament\Resources\LibraryLoans;

use App\Filament\Resources\LibraryLoans\Pages\CreateLibraryLoan;
use App\Filament\Resources\LibraryLoans\Pages\EditLibraryLoan;
use App\Filament\Resources\LibraryLoans\Pages\ListLibraryLoans;
use App\Filament\Resources\LibraryLoans\Schemas\LibraryLoanForm;
use App\Filament\Resources\LibraryLoans\Tables\LibraryLoansTable;
use App\Models\LibraryLoan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LibraryLoanResource extends Resource
{
    protected static ?string $model = LibraryLoan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Library';

    public static function form(Schema $schema): Schema
    {
        return LibraryLoanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryLoansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'bookCopy.book',
                'staffProfile',
                'studentProfile',
                'teacherProfile',
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibraryLoans::route('/'),
            'create' => CreateLibraryLoan::route('/create'),
            'edit' => EditLibraryLoan::route('/{record}/edit'),
        ];
    }
}
