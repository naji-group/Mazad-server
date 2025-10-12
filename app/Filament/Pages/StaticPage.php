<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
class StaticPage extends Page
{
    protected string $view = 'filament.pages.static-page';
  
    protected static ?string $slug = 'static-page';
       protected static ?string $recordTitleAttribute = 'الصفحة';
       protected static ?int $navigationSort = 5;
     
   protected static ?string $title = 'الصفحات الثابتة';
   protected static ?string $modelLabel = 'صفحة';
   protected static ?string $navigationLabel = 'الصفحات';
   protected static ?string $pluralModelLabel = 'الصفحات';
 
   public $settings;

   public function mount( ): void
   {

    $this->settings=Setting::where('category','pages')->get();
    
   }
   public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
   {
       return 'heroicon-o-document';
   }
   public static function isActive(): bool
   {
       
       return request()->routeIs('filament.pages.static-page');
   }
   public function getBreadcrumbs(): array
   {
       return [
           route('filament.admin.pages.dashboard') => 'لوحة التحكم',    
       'الصفحات الثابتة',
       ];
   }
}
