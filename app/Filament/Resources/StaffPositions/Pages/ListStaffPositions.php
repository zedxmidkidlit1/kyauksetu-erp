<?php

namespace App\Filament\Resources\StaffPositions\Pages;

use App\Filament\Resources\StaffPositions\StaffPositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStaffPositions extends ListRecords
{
    protected static string $resource = StaffPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
