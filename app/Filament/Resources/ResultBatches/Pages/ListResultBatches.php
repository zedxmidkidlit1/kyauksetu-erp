<?php

namespace App\Filament\Resources\ResultBatches\Pages;

use App\Filament\Resources\ResultBatches\ResultBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResultBatches extends ListRecords
{
    protected static string $resource = ResultBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
