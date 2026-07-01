<?php

namespace App\Filament\Resources\AdmissionApplications\Pages;

use App\Filament\Resources\AdmissionApplications\AdmissionApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdmissionApplication extends EditRecord
{
    protected static string $resource = AdmissionApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
