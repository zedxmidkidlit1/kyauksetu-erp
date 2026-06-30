<?php

namespace App\Filament\Resources\ExamTerms\Pages;

use App\Filament\Resources\ExamTerms\ExamTermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamTerms extends ListRecords
{
    protected static string $resource = ExamTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
