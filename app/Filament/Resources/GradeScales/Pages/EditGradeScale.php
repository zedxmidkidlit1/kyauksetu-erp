<?php

namespace App\Filament\Resources\GradeScales\Pages;

use App\Filament\Resources\GradeScales\GradeScaleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradeScale extends EditRecord
{
    protected static string $resource = GradeScaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
