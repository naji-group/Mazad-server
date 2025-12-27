<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
class AllLiveStreamsPage extends Page
{
    protected string $view = 'filament.pages.all-live-streams-page';
    protected static ?string $slug = 'livestreams';
    protected static ?string $recordTitleAttribute = 'البث المباشر';
    protected static ?int $navigationSort = 5;
  
protected static ?string $title = 'البث المباشر';
protected static ?string $modelLabel = 'البث المباشر';
protected static ?string $navigationLabel = 'البثوث المباشر';
protected static ?string $pluralModelLabel = 'البثوث المباشر';

public function mount( ): void
{


 
}
public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
{
    return 'heroicon-o-document';
}
public static function isActive(): bool
{
    
    return request()->routeIs('filament.pages.all-live-streams-page');
}
public function getBreadcrumbs(): array
{
    return [
        route('filament.admin.pages.dashboard') => 'لوحة التحكم',    
    'البثوث المباشرة',
    ];
}

// public static function getPages(): array
// {
//     return [
//         // ...
//         'auction' => AllLiveStreamsPage::route('/{record}/auction'),
//     ];
// }

}
