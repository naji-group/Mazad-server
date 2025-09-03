<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->default(null),
                Textarea::make('value')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('category')
                    ->default(null),
                TextInput::make('type')
                    ->default(null),
                TextInput::make('sequence')
                    ->numeric()
                    ->default(null),
                FileUpload::make('image')
                    ->image(),
                // TextInput::make('create_user_id')
                //     ->numeric()
                //     ->default(null),
                // TextInput::make('update_user_id')
                //     ->numeric()
                //     ->default(null),
            ]);
    }
}
