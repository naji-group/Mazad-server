<?php

namespace App\Filament\Resources\ExtraSocials\Schemas;

use App\Models\Social;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use App\Filament\Forms\Components\ImageWithPreview;
class ExtraSocialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                ->label('وسيلة التواصل')
                ->default(null),
                TextEntry::make('code')
                ->label('الرمز'),              
                // TextEntry::make('link')
                // ->url(fn (Social $record): string => $record->link)
                // ->openUrlInNewTab()
                // ->label('عنوان الحساب'),
                IconEntry::make('is_active')
                ->label('الحالة')
                ->boolean(),
                ImageWithPreview::make('local_image_preview')
                ->imageUrl(fn(Social $record) =>config('filesystems.disks.public.url').'/'. $record->icon )
                ->label('الصورة الحالية')
                // ->imageUrl($this->post?->getFirstMediaUrl( $this->media_folder) )
               // ->altText('Current Image')
                ->imageHeight(120)
               
                // ->live()
                ->nullable(),
             
            ]);
    }
}
