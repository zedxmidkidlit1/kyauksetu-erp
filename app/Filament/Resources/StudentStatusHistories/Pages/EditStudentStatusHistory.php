<?php

namespace App\Filament\Resources\StudentStatusHistories\Pages;

use App\Filament\Resources\StudentStatusHistories\StudentStatusHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentStatusHistory extends EditRecord
{
    protected static string $resource = StudentStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
