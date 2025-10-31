<?php

namespace App\Filament\Resources\ExtraSocials\Pages;

use App\Filament\Resources\ExtraSocials\ExtraSocialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExtraSocial extends CreateRecord
{
    protected static string $resource = ExtraSocialResource::class;
//     protected function mutateFormDataBeforeCreate(array $data): array
// {
//     $data['is_extra'] = true;

//     return $data;
// }
}
