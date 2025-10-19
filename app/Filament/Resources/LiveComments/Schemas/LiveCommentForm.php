<?php

namespace App\Filament\Resources\LiveComments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LiveCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('marketer_id')
                    ->numeric()
                    ->default(null),
                Select::make('live_stream_id')
                 //   ->relationship('liveStream', 'name')
                    ->default(null),
                TextInput::make('agora_live_id')
                    ->default(null),
                TextInput::make('platform')
                    ->required(),
                TextInput::make('comment_id')
                    ->required(),
                TextInput::make('author_name')
                    ->default(null),
                Textarea::make('message')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('comment_time'),
            ]);
    }
}
