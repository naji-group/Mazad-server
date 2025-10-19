<?php

namespace App\Filament\Resources\LiveComments\Pages;

use App\Filament\Resources\LiveComments\LiveCommentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLiveComment extends EditRecord
{
    protected static string $resource = LiveCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
