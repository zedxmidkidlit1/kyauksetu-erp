<?php

namespace App\Filament\Resources\AdmissionBatches\Pages;

use App\Filament\Resources\AdmissionBatches\AdmissionBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionBatches extends ListRecords
{
    protected static string $resource = AdmissionBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
