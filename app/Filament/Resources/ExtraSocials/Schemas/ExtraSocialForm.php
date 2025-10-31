<?php

namespace App\Filament\Resources\ExtraSocials\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use SebastianBergmann\Type\TrueType;
use Filament\Forms\Components\FileUpload;
use App\Filament\Forms\Components\ImageWithPreview;
use App\Models\Social;
class ExtraSocialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('وسيلة التواصل')
                    ->required()
                    //->default(null)
                    ,
                TextInput::make('code')
                    ->label('الرمز')
                    ->default(null)
                    ->required()
                    ,
                // TextInput::make('link')
                //     ->label('عنوان الحساب')
                //     ->required()
                //     ->default(null),
                Toggle::make('is_active')
                    ->label('مفعل'),
                 
                FileUpload::make('icon')
                    ->label('صورة')
                    ->image()
                    ->previewable(false)
                    // ->fetchFileInformation(false)
                    ->disk('public')
                    ->directory('images/socials')
                    ->visibility('public')
                // -> previewable()
                ,
                ImageWithPreview::make('image_preview')
                ->imageUrl(fn(Social $record) => config('filesystems.disks.public.url') . '/' . $record->icon)
                ->label('الصورة الحالية')
                // ->imageUrl($this->post?->getFirstMediaUrl( $this->media_folder) )
                // ->altText('Current Image')
                ->imageHeight(120)
                ->hiddenOn('create')
                // ->live()
                ->nullable(),
                    Hidden::make('is_extra')
                    ->default(true)

            ]);
    }
}
