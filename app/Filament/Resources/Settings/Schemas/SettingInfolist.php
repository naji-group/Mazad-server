<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('category'),
                TextEntry::make('type'),
                TextEntry::make('sequence')
                    ->numeric(),
                ImageEntry::make('image'),
                // TextEntry::make('create_user_id')
                //     ->numeric(),
                // TextEntry::make('update_user_id')
                //     ->numeric(),
                // TextEntry::make('created_at')
                //     ->dateTime(),
                // TextEntry::make('updated_at')
                //     ->dateTime(),
            ]);
    }
}
