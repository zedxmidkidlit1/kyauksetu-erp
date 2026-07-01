<?php

namespace App\Filament\Resources\AdmissionBatches\Pages;

use App\Filament\Resources\AdmissionBatches\AdmissionBatchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionBatch extends EditRecord
{
    protected static string $resource = AdmissionBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
