<?php

namespace App\Filament\Resources\Marketers\Pages;

use App\Filament\Resources\Marketers\MarketerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMarketer extends ViewRecord
{
    protected static string $resource = MarketerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
