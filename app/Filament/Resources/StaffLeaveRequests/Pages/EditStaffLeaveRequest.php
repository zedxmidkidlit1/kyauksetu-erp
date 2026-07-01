<?php

namespace App\Filament\Resources\StaffLeaveRequests\Pages;

use App\Filament\Resources\StaffLeaveRequests\StaffLeaveRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaffLeaveRequest extends EditRecord
{
    protected static string $resource = StaffLeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
