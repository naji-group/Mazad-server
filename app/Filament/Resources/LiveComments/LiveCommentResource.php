<?php

namespace App\Filament\Resources\LiveComments;

use App\Filament\Resources\LiveComments\Pages\CreateLiveComment;
use App\Filament\Resources\LiveComments\Pages\EditLiveComment;
use App\Filament\Resources\LiveComments\Pages\ListLiveComments;
use App\Filament\Resources\LiveComments\Pages\ViewLiveComment;
use App\Filament\Resources\LiveComments\Schemas\LiveCommentForm;
use App\Filament\Resources\LiveComments\Schemas\LiveCommentInfolist;
use App\Filament\Resources\LiveComments\Tables\LiveCommentsTable;
use App\Models\LiveComment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveCommentResource extends Resource
{
    protected static ?string $model = LiveComment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Comment';

    public static function form(Schema $schema): Schema
    {
        return LiveCommentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LiveCommentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveCommentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->role=='admin';
    }
    public static function getPages(): array
    {
        return [
            'index' => ListLiveComments::route('/'),
            'create' => CreateLiveComment::route('/create'),
            'view' => ViewLiveComment::route('/{record}'),
            'edit' => EditLiveComment::route('/{record}/edit'),
        ];
    }
}
