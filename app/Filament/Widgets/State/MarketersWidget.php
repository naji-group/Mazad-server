<?php

namespace App\Filament\Widgets\State;

use App\Models\LiveStream;
use App\Models\Marketer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;
class MarketersWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1; 
    protected function getStats(): array
    {
        $m_count=Marketer::where('is_active',1)->count();
        $l_count=LiveStream::count();
        $now_count=LiveStream::where('is_active',1)->count();
        return [
            Stat::make('المسوقين',  $m_count.' مسوق ')
           // ->description('مسوق')
          //  ->descriptionIcon('heroicon-m-user', IconPosition::Before)
           // ->color('danger')
            ->extraAttributes([
                'style' => 'text-align: center',
               
            ]),
            Stat::make('البثوث',  $l_count.' بث ')
            //->description('بث')
            ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
            ->color('danger')
            ->extraAttributes([
                'style' => 'text-align: center',
               
            ]),
            Stat::make('البثوث المباشرة',  $now_count.' بث مباشر ')
            //->description('بث')
            ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
            ->color('danger')
            ->extraAttributes([
                'style' => 'text-align: center',
               
            ]),
        ];
    }
}
