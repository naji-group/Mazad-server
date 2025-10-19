<?php

namespace App\Filament\Resources\LiveComments\Pages;

use App\Filament\Resources\LiveComments\LiveCommentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLiveComments extends ListRecords
{
    protected static string $resource = LiveCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
