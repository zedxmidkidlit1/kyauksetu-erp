<?php

namespace App\Filament\Resources\AdmissionApplications\Pages;

use App\Filament\Resources\AdmissionApplications\AdmissionApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmissionApplications extends ListRecords
{
    protected static string $resource = AdmissionApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
