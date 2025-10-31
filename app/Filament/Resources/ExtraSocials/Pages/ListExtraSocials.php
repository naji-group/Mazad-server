<?php

namespace App\Filament\Resources\ExtraSocials\Pages;

use App\Filament\Resources\ExtraSocials\ExtraSocialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtraSocials extends ListRecords
{
    protected static string $resource = ExtraSocialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
