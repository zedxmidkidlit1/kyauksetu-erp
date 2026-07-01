<?php

namespace App\Filament\Resources\StaffEmployments\Pages;

use App\Filament\Resources\StaffEmployments\StaffEmploymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaffEmployment extends EditRecord
{
    protected static string $resource = StaffEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
