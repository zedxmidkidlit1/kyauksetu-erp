<?php

namespace App\Filament\Resources\AdmissionDecisions\Pages;

use App\Filament\Resources\AdmissionDecisions\AdmissionDecisionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionDecision extends EditRecord
{
    protected static string $resource = AdmissionDecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
