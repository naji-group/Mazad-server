<?php

namespace App\Filament\Resources\Marketers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MarketerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->default(null),
                TextInput::make('last_name')
                    ->default(null),
                TextInput::make('username')
                    ->default(null),
                TextInput::make('password')
                    ->password()
                    ->default(null),
                TextInput::make('is_active')
                    ->numeric()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('avatar')
                    ->default(null),
                TextInput::make('provider')
                    ->default(null),
                TextInput::make('provider_user_id')
                    ->default(null),
                Select::make('social_id')
                    ->relationship('social', 'name')
                    ->default(null),
                DateTimePicker::make('email_verified_at'),
            ]);
    }
}
