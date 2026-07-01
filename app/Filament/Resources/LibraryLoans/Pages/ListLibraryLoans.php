<?php

namespace App\Filament\Resources\LibraryLoans\Pages;

use App\Filament\Resources\LibraryLoans\LibraryLoanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLibraryLoans extends ListRecords
{
    protected static string $resource = LibraryLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
