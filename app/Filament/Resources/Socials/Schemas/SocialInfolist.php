<?php

namespace App\Filament\Resources\Socials\Schemas;

use App\Models\Social;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
class SocialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ar_name')
                ->label('وسيلة التواصل')
                ->default(null),
                TextEntry::make('name')
                ->label('الرمز'),              
                TextEntry::make('link')
                ->url(fn (Social $record): string => $record->link)
                ->openUrlInNewTab()
                ->label('عنوان الحساب'),
                IconEntry::make('is_active')
                ->label('الحالة')
                ->boolean()
            ,
             
            ]);
    }
}
