<?php

namespace App\Filament\Resources\AdmissionDocuments\Pages;

use App\Filament\Resources\AdmissionDocuments\AdmissionDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionDocuments extends ListRecords
{
    protected static string $resource = AdmissionDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
