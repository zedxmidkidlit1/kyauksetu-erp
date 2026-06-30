<?php

namespace App\Filament\Resources\AssessmentComponents\Pages;

use App\Filament\Resources\AssessmentComponents\AssessmentComponentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentComponent extends EditRecord
{
    protected static string $resource = AssessmentComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
