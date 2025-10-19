<?php

namespace App\Filament\Resources\LiveStreams\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LiveStreamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('marketer.name')
                    ->numeric(),
                TextEntry::make('agora_live_id'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('youtube_live_chat_id'),
                TextEntry::make('facebook_live_video_id'),
                TextEntry::make('instagram_live_video_id'),
                TextEntry::make('tiktok_live_video_id'),
                TextEntry::make('jaco_live_video_id'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
