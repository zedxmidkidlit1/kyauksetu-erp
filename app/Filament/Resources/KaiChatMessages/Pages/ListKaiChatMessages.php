<?php

namespace App\Filament\Resources\KaiChatMessages\Pages;

use App\Filament\Resources\KaiChatMessages\KaiChatMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListKaiChatMessages extends ListRecords
{
    protected static string $resource = KaiChatMessageResource::class;
}
