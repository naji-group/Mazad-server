<?php

namespace App\Filament\Resources\Socials;

use App\Filament\Resources\Socials\Pages\CreateSocial;
use App\Filament\Resources\Socials\Pages\EditSocial;
use App\Filament\Resources\Socials\Pages\ListSocials;
use App\Filament\Resources\Socials\Pages\ViewSocial;
use App\Filament\Resources\Socials\Schemas\SocialForm;
use App\Filament\Resources\Socials\Schemas\SocialInfolist;
use App\Filament\Resources\Socials\Tables\SocialsTable;
use App\Models\Social;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SocialResource extends Resource
{
    protected static ?string $model = Social::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'وسائل التواصل';
     
    
    protected static ?string $title = 'وسائل التواصل';
    protected static ?string $modelLabel = 'وسيلة تواصل';
    protected static ?string $navigationLabel = 'وسائل التواصل';
    protected static ?string $pluralModelLabel = 'وسائل التواصل';
    protected static ?int $navigationSort = 3;
    public static function form(Schema $schema): Schema
    {
        return SocialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SocialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocials::route('/'),
            'create' => CreateSocial::route('/create'),
            'view' => ViewSocial::route('/{record}'),
            'edit' => EditSocial::route('/{record}/edit'),
        ];
    }
}
