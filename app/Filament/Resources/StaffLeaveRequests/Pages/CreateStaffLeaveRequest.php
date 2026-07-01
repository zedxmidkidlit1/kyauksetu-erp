<?php

namespace App\Filament\Resources\StaffLeaveRequests\Pages;

use App\Filament\Resources\StaffLeaveRequests\StaffLeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffLeaveRequest extends CreateRecord
{
    protected static string $resource = StaffLeaveRequestResource::class;
}
