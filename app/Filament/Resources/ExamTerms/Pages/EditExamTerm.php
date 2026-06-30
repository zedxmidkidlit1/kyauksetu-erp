<?php

namespace App\Filament\Resources\ExamTerms\Pages;

use App\Filament\Resources\ExamTerms\ExamTermResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamTerm extends EditRecord
{
    protected static string $resource = ExamTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
