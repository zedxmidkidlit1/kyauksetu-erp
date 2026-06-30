<?php

namespace App\Filament\Resources\ResultBatches\Pages;

use App\Filament\Resources\ResultBatches\ResultBatchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResultBatch extends EditRecord
{
    protected static string $resource = ResultBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
