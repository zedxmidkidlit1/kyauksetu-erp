<?php

namespace App\Filament\Resources\AdmissionDecisions\Pages;

use App\Filament\Resources\AdmissionDecisions\AdmissionDecisionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionDecisions extends ListRecords
{
    protected static string $resource = AdmissionDecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
