<?php

namespace App\Filament\Pages;

use App\Models\LiveStream;
use Filament\Pages\Page;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
//use Filament\Facades\Filament;
class LiveStreamPage extends Page
{
    protected string $view = 'filament.pages.live-stream-page';
    protected static bool $shouldRegisterNavigation = false;
   
    protected static ?string $slug = 'livestream/{id}';
    protected static ?string $recordTitleAttribute = 'البث المباشر';
   
  
protected static ?string $title = 'البث المباشر';
protected static ?string $modelLabel = 'البث المباشر';
//protected static ?string $navigationLabel = 'البثوث المباشر';
protected static ?string $pluralModelLabel = 'البثوث المباشرة';
public LiveStream $livestream;

public function mount(int $id): void
    {
      //  \Log::info('livrest',[$id]);

        $this->livestream = LiveStream::findOrFail($id);
      //  \Log::info('livrest',[ $this->livestream->is_active]);
    }
 
// public static function isActive(): bool
// {
    
//     return request()->routeIs('filament.pages.all-live-streams-page');
// }
public function getBreadcrumbs(): array
{
    return [
        route('filament.admin.pages.dashboard') => 'لوحة التحكم',    
    AllLiveStreamsPage::getUrl()=> 'البثوث المباشرة'
    ];
}
}
