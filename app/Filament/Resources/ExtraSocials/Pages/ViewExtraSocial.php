<?php

namespace App\Filament\Resources\ExtraSocials\Pages;

use App\Filament\Resources\ExtraSocials\ExtraSocialResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExtraSocial extends ViewRecord
{
    protected static string $resource = ExtraSocialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
