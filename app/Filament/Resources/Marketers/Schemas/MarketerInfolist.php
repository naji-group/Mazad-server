<?php

namespace App\Filament\Resources\Marketers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use App\Models\Marketer;
use App\Filament\Forms\Components\ImageWithPreview;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
 
use App\Models\Social;
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

                Section::make('الحسابات الاجتماعية')
               
                ->schema(function (Marketer $record) {
                    $fields = [];

                    foreach (
                        Social::with([
                            'marketersocials' => function ($q) use ($record) {
                                $q->where('marketer_id', $record->id);
                            }
                        ])->orderBy('sequence')->get() as $social
                    ) {
                        $marketerSocial = $social->marketersocials?->first();

                        $fields[] = Group::make([
                            TextEntry::make("socials.{$social->id}.link")
                                ->label("رابط {$social->name}")
                                ->default($marketerSocial?->link ?? '-')
                                ->url(fn () => $marketerSocial?->link, true) // عرض كرابط
                                ->columnSpan(3),

                            IconEntry::make("socials.{$social->id}.is_active")
                                ->label("تفعيل {$social->name}")
                                ->boolean()
                                ->default((bool) $marketerSocial?->is_active)
                                ->columnSpan(1),
                        ])->columns(4)->columnSpanFull();
                    }

                    return $fields;
                })->columnSpanFull(),
            ]);
    }
}
