<?php

namespace App\Filament\Resources\Marketers;

use App\Filament\Resources\Marketers\Pages\CreateMarketer;
use App\Filament\Resources\Marketers\Pages\EditMarketer;
use App\Filament\Resources\Marketers\Pages\ListMarketers;
use App\Filament\Resources\Marketers\Pages\ViewMarketer;
use App\Filament\Resources\Marketers\Schemas\MarketerForm;
use App\Filament\Resources\Marketers\Schemas\MarketerInfolist;
use App\Filament\Resources\Marketers\Tables\MarketersTable;
use App\Models\Marketer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MarketerResource extends Resource
{
    protected static ?string $model = Marketer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    

    protected static ?string $recordTitleAttribute = 'مسوق';
    
    protected static ?string $title = 'المسوقين';
    protected static ?string $modelLabel = 'مسوق';
    protected static ?string $navigationLabel = 'المسوقين';
    protected static ?string $pluralModelLabel = 'المسوقين';
    protected static ?int $navigationSort = 2;
    public static function form(Schema $schema): Schema
    {
        return MarketerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketersTable::configure($table);
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
            'index' => ListMarketers::route('/'),
           // 'create' => CreateMarketer::route('/create'),
            'view' => ViewMarketer::route('/{record}'),
          //  'edit' => EditMarketer::route('/{record}/edit'),
        ];
    }
}
