<?php

namespace App\Filament\Resources\StudentCourseResults\Pages;

use App\Filament\Resources\StudentCourseResults\StudentCourseResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentCourseResults extends ListRecords
{
    protected static string $resource = StudentCourseResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
