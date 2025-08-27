<?php

namespace App\Filament\Resources\Socials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SocialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->default(null),
                TextInput::make('ar_name')
                    ->default(null),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('link')
                    ->default(null),
                TextInput::make('is_active')
                    ->numeric()
                    ->default(null),
                TextInput::make('icon')
                    ->default(null),
            ]);
    }
}
