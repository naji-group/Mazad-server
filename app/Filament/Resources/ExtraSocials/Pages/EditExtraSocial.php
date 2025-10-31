<?php

namespace App\Filament\Resources\ExtraSocials\Pages;

use App\Filament\Resources\ExtraSocials\ExtraSocialResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExtraSocial extends EditRecord
{
    protected static string $resource = ExtraSocialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
