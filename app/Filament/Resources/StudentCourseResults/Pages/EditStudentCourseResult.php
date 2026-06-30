<?php

namespace App\Filament\Resources\StudentCourseResults\Pages;

use App\Filament\Resources\StudentCourseResults\StudentCourseResultResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentCourseResult extends EditRecord
{
    protected static string $resource = StudentCourseResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
