<?php

namespace App\Filament\Widgets\Chart;

use App\Models\LiveStream;
use App\Models\Marketer;
use Filament\Widgets\ChartWidget;

class LiveChart extends ChartWidget
{
    protected ?string $heading = 'عدد البثوث';
    protected static ?int $sort = 2; 
    protected function getData(): array
    {
        $marketers = Marketer::where('is_active',1)->get();
      $data=[]; 
      $label=[]; 
foreach ($marketers as $marketer) {
   $lives_count= LiveStream::where('marketer_id',$marketer->id)->count();
   $data[]=$lives_count;
   $label[]=$marketer->username; 
}
        return [
            'datasets' => [
                [
                    'label' => 'عدد البثوث',
                    'data' =>$data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $label,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
}
