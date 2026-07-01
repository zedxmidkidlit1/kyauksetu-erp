<?php

namespace App\Filament\Resources\StaffPositions\Pages;

use App\Filament\Resources\StaffPositions\StaffPositionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaffPosition extends EditRecord
{
    protected static string $resource = StaffPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
