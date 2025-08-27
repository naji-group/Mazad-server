<?php

namespace App\Filament\Resources\Marketers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MarketerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('username'),
                TextEntry::make('is_active')
                    ->numeric(),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('avatar'),
                TextEntry::make('provider'),
                TextEntry::make('provider_user_id'),
                TextEntry::make('social.name')
                    ->numeric(),
                TextEntry::make('email_verified_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
