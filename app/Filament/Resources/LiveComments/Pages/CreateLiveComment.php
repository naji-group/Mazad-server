<?php

namespace App\Filament\Resources\LiveComments\Pages;

use App\Filament\Resources\LiveComments\LiveCommentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLiveComment extends CreateRecord
{
    protected static string $resource = LiveCommentResource::class;
}
