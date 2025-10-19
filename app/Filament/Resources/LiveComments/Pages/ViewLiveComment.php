<?php

namespace App\Filament\Resources\LiveComments\Pages;

use App\Filament\Resources\LiveComments\LiveCommentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLiveComment extends ViewRecord
{
    protected static string $resource = LiveCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
