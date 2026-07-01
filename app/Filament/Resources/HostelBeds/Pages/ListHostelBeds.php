<?php

namespace App\Filament\Resources\HostelBeds\Pages;

use App\Filament\Resources\HostelBeds\HostelBedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHostelBeds extends ListRecords
{
    protected static string $resource = HostelBedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
