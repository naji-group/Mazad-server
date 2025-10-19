<?php

namespace App\Filament\Resources\LiveComments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LiveCommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('marketer_id')
                    ->numeric(),
                TextEntry::make('liveStream.name')
                    ->numeric(),
                TextEntry::make('agora_live_id'),
                TextEntry::make('platform'),
                TextEntry::make('comment_id'),
                TextEntry::make('author_name'),
                TextEntry::make('comment_time')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
