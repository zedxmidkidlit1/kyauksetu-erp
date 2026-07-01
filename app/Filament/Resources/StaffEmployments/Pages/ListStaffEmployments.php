<?php

namespace App\Filament\Resources\StaffEmployments\Pages;

use App\Filament\Resources\StaffEmployments\StaffEmploymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaffEmployments extends ListRecords
{
    protected static string $resource = StaffEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
