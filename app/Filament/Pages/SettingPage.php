<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
 

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
class SettingPage extends Page
{
    
     
    protected string $view = 'filament.pages.setting-page';
    protected static ?string $slug = 'setting-page';
       protected static ?string $recordTitleAttribute = 'اعدادات';
       protected static ?int $navigationSort = 6;
     

      
   protected static ?string $title = 'الاعدادات';
   protected static ?string $modelLabel = 'اعدادات';
   protected static ?string $navigationLabel = 'الاعدادات';
   protected static ?string $pluralModelLabel = 'الاعدادات';
 
   public $settings;

   public function mount( ): void
   {

    $this->settings=Setting::whereNot('category','pages')->get();
    
   }
   public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
   {
       return 'heroicon-o-cog';
   }
   public static function isActive(): bool
   {
       
       return request()->routeIs('filament.pages.setting-page');
   }
   public function getBreadcrumbs(): array
   {
       return [
           route('filament.admin.pages.dashboard') => 'لوحة التحكم',    
       'الاعدادات',
       ];
   }
}
