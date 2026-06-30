<?php

namespace App\Filament\Resources\StudentStatusHistories\Pages;

use App\Filament\Resources\StudentStatusHistories\StudentStatusHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentStatusHistories extends ListRecords
{
    protected static string $resource = StudentStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
