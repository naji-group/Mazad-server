<?php

namespace App\Filament\Resources\Marketers\Pages;

use App\Filament\Resources\Marketers\MarketerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMarketers extends ListRecords
{
    protected static string $resource = MarketerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
