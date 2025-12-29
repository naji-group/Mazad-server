<?php

namespace App\Livewire\LiveStream;

use App\Models\Auction;
use App\Models\LiveStream;
use Livewire\Component;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class AllStatistic extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public LiveStream $livestream;
    public function render()
    {
        return view('livewire.live-stream.all-statistic');
    }



    public function mount(LiveStream $livestream): void
    {
        $this->livestream = $livestream;
        // $this->livestream_id= $livestream->id;
    }
    public function liveInfolist(Schema $schema): Schema
    {
        $comments= Auction::where('live_video_id',  $this->livestream->id);

        $comments_count= $comments->count();
        $comments_min= $comments->min('price');
        $comments_max= $comments->max('price');
        return $schema
            ->record($this->livestream)
            ->components([
                Section::make('معلومات البث')
                ->heading('معلومات البث')
              
                    ->inlineLabel()
                    ->components([
                        TextEntry::make('marketer.username')
                            ->label('المسوق')
                            ->columnSpanFull(),
                        TextEntry::make('start_date')
                            ->dateTime()
                            ->label('تاريخ البدء'),
                        TextEntry::make('end_date')
                            ->dateTime()
                            ->label('تاريخ الانتهاء'),
                        TextEntry::make('live_duration')
                          
                            ->label('المدة'),
                            TextEntry::make('comments_counts')                          
                            ->label('عدد المزايدات')
                            ->state(function () use ($comments_count) {
                                return $comments_count;
                            }),
                            TextEntry::make('comments_min')                          
                            ->label('ادنى سعر')
                            ->state(function () use ($comments_min) {
                                return $comments_min;
                            }),
                            TextEntry::make('comments_max')                          
                            ->label('اعلى سعر')
                            ->state(function () use ($comments_max) {
                                return $comments_max;
                            }),

                    ])->columns(3)
                    , 
            ]);
    }



}
