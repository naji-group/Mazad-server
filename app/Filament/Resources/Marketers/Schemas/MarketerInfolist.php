<?php

namespace App\Filament\Resources\Marketers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use App\Models\Marketer;
use App\Filament\Forms\Components\ImageWithPreview;
class MarketerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('username')
                    ->label('اسم المستخدم'),
                IconEntry::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                ,
                TextEntry::make('email')
                    ->label('الايميل'),
                // TextEntry::make('avatar'),
                // TextEntry::make('provider'),
                // TextEntry::make('provider_user_id'),
                // TextEntry::make('social.name')
                //     ->numeric(),

                TextEntry::make('created_at')
                    ->label('تاريخ الانشاء')
                    ->dateTime()
                ,
                ImageWithPreview::make('local_image_preview')
                ->imageUrl(fn(Marketer $record) =>config('filesystems.disks.public.url').'/'. $record->local_image )
                ->label('الصورة الحالية')
                // ->imageUrl($this->post?->getFirstMediaUrl( $this->media_folder) )
               // ->altText('Current Image')
                ->imageHeight(120)
               ->hiddenOn('create') 
                // ->live()
                ->nullable(),
                // TextEntry::make('updated_at')
                //     ->dateTime(),
            ]);
    }
}
