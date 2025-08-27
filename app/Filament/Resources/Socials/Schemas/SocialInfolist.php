<?php

namespace App\Filament\Resources\Socials\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SocialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('ar_name'),
                TextEntry::make('link'),
                TextEntry::make('is_active')
                    ->numeric(),
                TextEntry::make('icon'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
