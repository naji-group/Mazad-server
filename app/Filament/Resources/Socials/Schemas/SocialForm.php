<?php

namespace App\Filament\Resources\Socials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
class SocialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('وسيلة التواصل')
                    ->default(null),
                TextInput::make('code')
                    ->label('الرمز')
                    ->default(null)
                    ->required()
                ,
                TextInput::make('link')
                    ->label('عنوان الحساب')
                    ->required()
                    ->default(null),
                Toggle::make('is_active')
                    ->label('مفعل'),

            ]);
    }
}
