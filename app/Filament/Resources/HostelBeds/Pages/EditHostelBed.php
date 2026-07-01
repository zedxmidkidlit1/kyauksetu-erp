<?php

namespace App\Filament\Resources\HostelBeds\Pages;

use App\Filament\Resources\HostelBeds\HostelBedResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHostelBed extends EditRecord
{
    protected static string $resource = HostelBedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
