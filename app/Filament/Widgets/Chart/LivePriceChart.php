<?php

namespace App\Filament\Widgets\Chart;

use App\Models\Auction;
use App\Models\LiveStream;
use Filament\Widgets\ChartWidget;
 
use Illuminate\Support\Facades\DB;
class LivePriceChart extends ChartWidget
{
    protected ?string $heading = 'اعلى الاسعار';
    protected static ?int $sort = 3; 
    protected function getData(): array
    {
      
      $data=[]; 
      $label=[]; 

   

    //   $tops = DB::table('auctions')
    // ->select('live_video_id', DB::raw('MAX(price) as max_price'))    
    // ->groupBy('live_video_id')->limit(10)->get();

    $tops = DB::table('auctions as a')
    ->join(DB::raw('(
        SELECT live_video_id, MAX(price) as max_price
        FROM auctions
        GROUP BY live_video_id
    ) as max_prices'), function($join) {
        $join->on('a.live_video_id', '=', 'max_prices.live_video_id')
             ->on('a.price', '=', 'max_prices.max_price');
    })
    ->whereIn('a.id', function($query) {
        $query->select(DB::raw('MIN(id)'))
            ->from('auctions as a2')
            ->whereRaw('a2.price = (
                SELECT MAX(price) 
                FROM auctions 
                WHERE live_video_id = a2.live_video_id
            )')
            ->groupBy('live_video_id');
    })
    ->select('a.id', 'a.customer_name', 'a.live_video_id', 'a.price as max_price')
    ->orderBy('a.price', 'DESC')
    ->limit(10)
    ->get();

// \Log::info("auction",$tops->toArray());


foreach ($tops as $auction) {

   $data[]=$auction->max_price;
   $label[]=$auction->customer_name; 
}
        return [
            'datasets' => [
                [
                    'label' => 'السعر ',
                    'data' =>$data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $label,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
}
