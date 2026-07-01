<?php

namespace App\Filament\Resources\StudentFees\Pages;

use App\Filament\Resources\StudentFees\StudentFeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentFee extends CreateRecord
{
    protected static string $resource = StudentFeeResource::class;
}
