<?php

namespace App\Livewire\LiveStream;

use App\Models\Auction;
use App\Models\LivestreamSocial;
use App\Models\Social;
use Filament\Schemas\Components\Grid;
use Google\Service\BackupforGKE\Label;
use Livewire\Component;
use App\Models\LiveStream; 
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Pest\Mutate\Mutators\Logical\TrueToFalse;
 
class SocialStatistic extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public LiveStream $livestream;
    public function render()
    {
        return view('livewire.live-stream.social-statistic');
    }


    public function mount(LiveStream $livestream): void
    {
        $this->livestream = $livestream;
        // $this->livestream_id= $livestream->id;
    }
    public function socialsInfolist(Schema $schema): Schema
    {
        $socials=Social::orderBy('sequence')->get();
        $all_sections=[];
      //  $livestreamsocials=LivestreamSocial::where('live_stream_id', $this->livestream->id)->get();
      $comments= Auction::where('live_video_id',$this->livestream->id)->get();

        foreach ($socials as $social) {
            $comment_social= $comments->where('social_id',$social->id);

            $comments_count= $comment_social->count();
            $comments_min= $comment_social->min('price');
            $comments_max= $comment_social->max('price');

            $livestreamsocial = $this->livestream->livestreamsocials
                ->where('social_id', $social->id)
                ->first();
            if( $livestreamsocial||$comments_count){

                $section = Section::make('احصائيات ' . $social->name)
                ->inlineLabel()
                ->columnSpan(1)
                ->schema([
                    Grid::make( )
                        ->schema([
                            TextEntry::make("start_date_{$social->id}")
                            ->label('تاريخ البدء')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->start_date;
                            })
                            ->dateTime(),
                            TextEntry::make("end_date_{$social->id}")
                            ->label('تاريخ الانتهاء')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->end_date;
                            })
                            ->dateTime(),
                        TextEntry::make("duration_str_{$social->id}")
                            ->label('المدة')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->duration_str;
                            })
                         ,  TextEntry::make("comments_{$social->id}")
                            ->label('التعليقات')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->real_comments_count ?? '0';
                            }), 
                            TextEntry::make("views_count_{$social->id}")
                            ->label('المشاهدات')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->views_count ?? '0';
                            }),
                         
                            TextEntry::make("likes_count_{$social->id}")
                            ->label('الاعجابات')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->likes_count ?? '0';
                            }),
                            TextEntry::make("dislike_count_{$social->id}")
                            ->label('عدم الاعجاب')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->dislike_count ?? '0';
                            })->hidden($social->code=="tiktok"),
                            TextEntry::make("favorite_count_{$social->id}")
                            ->label('المفضلة')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->favorite_count ?? '0';
                            })->hidden($social->code=="tiktok")  ,                            

                            TextEntry::make("followers_count_{$social->id}")
                            ->label('المتابعين')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->followers_count ?? '0';
                            })->hidden($social->code!="tiktok"),
                            TextEntry::make("shares_count_{$social->id}")
                            ->label('المشاركات')
                            ->state(function () use ($livestreamsocial) {
                                return $livestreamsocial?->shares_count ?? '0';
                            })->hidden($social->code!="tiktok"),
                        
                        ])   ->columns(2) ->hidden($social->is_extra==1)  
                   ,  Grid::make( )
                   ->schema([
                        TextEntry::make("comments_counts_{$social->id}")                          
                        ->label('المزايدات')
                        ->state(function () use ($comments_count) {
                            return $comments_count;
                        })->columnSpanFull(),
                        TextEntry::make("comments_min_{$social->id}")                          
                        ->label('ادنى سعر')
                        ->state(function () use ($comments_min) {
                            return $comments_min;
                        })->columnSpan(1),
                        TextEntry::make("comments_max_{$social->id}")                          
                        ->label('اعلى سعر')
                        ->state(function () use ($comments_max) {
                            return $comments_max;
                        })->columnSpan(1),
                        ]) ->columns(2)
                ]);
            }else{
                $section = Section::make('احصائيات ' . $social->name)
                ->inlineLabel()
                ->columnSpan(1)
                ->schema([
                    TextEntry::make("empty_{$social->id}")
          //  ->label(null) // تترك label فارغ
            ->state('لا يوجد احصائيات')
            ->extraAttributes(['class' => 'text-center']) // توسيط النص
            ->hiddenLabel()
             ])
                  
                ;
            }
           
            
            $all_sections[] = $section;
        }
        return $schema
            ->record($this->livestream)
            ->columns(2)
            ->components($all_sections ) ;
    }
  
}
