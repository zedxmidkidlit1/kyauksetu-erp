<?php

namespace App\Filament\Resources\LibraryLoans\Pages;

use App\Filament\Resources\LibraryLoans\LibraryLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLibraryLoan extends CreateRecord
{
    protected static string $resource = LibraryLoanResource::class;
}
