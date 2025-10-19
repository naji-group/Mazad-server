<?php

namespace App\Filament\Resources\LiveStreams\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LiveStreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('marketer_id')
                 //   ->relationship('marketer', 'name')
                    ->default(null),
                TextInput::make('agora_live_id')
                    ->required(),
                Toggle::make('is_active'),
                TextInput::make('youtube_live_chat_id')
                    ->default(null),
                TextInput::make('facebook_live_video_id')
                    ->default(null),
                TextInput::make('instagram_live_video_id')
                    ->default(null),
                TextInput::make('tiktok_live_video_id')
                    ->default(null),
                TextInput::make('jaco_live_video_id')
                    ->default(null),
            ]);
    }
}
