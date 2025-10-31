<?php

namespace App\Filament\Resources\ExtraSocials;

use App\Filament\Resources\ExtraSocials\Pages\CreateExtraSocial;
use App\Filament\Resources\ExtraSocials\Pages\EditExtraSocial;
use App\Filament\Resources\ExtraSocials\Pages\ListExtraSocials;
use App\Filament\Resources\ExtraSocials\Pages\ViewExtraSocial;
use App\Filament\Resources\ExtraSocials\Schemas\ExtraSocialForm;
use App\Filament\Resources\ExtraSocials\Schemas\ExtraSocialInfolist;
use App\Filament\Resources\ExtraSocials\Tables\ExtraSocialsTable;
use App\Models\Social;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExtraSocialResource extends Resource
{
    protected static ?string $model = Social::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'وسائل التواصل';
     
    
    protected static ?string $title = 'وسائل التواصل';
    protected static ?string $modelLabel = 'وسيلة تواصل';
    protected static ?string $navigationLabel = 'الوسائل الاضافية';
    protected static ?string $pluralModelLabel = 'وسائل التواصل';
    protected static ?int $navigationSort = 4;
    public static function form(Schema $schema): Schema
    {
        return ExtraSocialForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExtraSocialInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtraSocialsTable::configure($table);
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
            'index' => ListExtraSocials::route('/'),
          // 'create' => CreateSocial::route('/create'),
            'view' => ViewExtraSocial::route('/{record}'),
           // 'edit' => EditSocial::route('/{record}/edit'),
        ];
    }
}
