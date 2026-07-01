<?php

namespace App\Filament\Resources\LibraryLoans\Pages;

use App\Filament\Resources\LibraryLoans\LibraryLoanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLibraryLoan extends EditRecord
{
    protected static string $resource = LibraryLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
