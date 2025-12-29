<?php

namespace App\Livewire\LiveStream;

use App\Models\Auction;
use App\Models\LiveStream;
use App\Models\Social;
//use Filament\Schemas\Components\Tabs\Tab;
use Livewire\Component;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;

use Illuminate\Contracts\View\View;

use Illuminate\Database\Eloquent\Builder;

// use Filament\Actions\Action;
// use Filament\Tables\Filters\SelectFilter;
// use Filament\Tables\Filters\Filter;

// use Filament\Tables\Enums\FiltersLayout;
// use Filament\Tables\Filters\BaseFilter;
// use Filament\Forms\Components\ToggleButtons;
// use Filament\Forms\Components\Select;

// use Filament\Resources\Pages\ListRecords\Tab;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Tabs\Tab;
use Filament\Tables\Filters\Filter;

class CommentsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function render()
    {
        return view('livewire.live-stream.comments-table');
    }
    public LiveStream $livestream;

    public int $livestream_id;
    public array $socialTabs = [];
    public string $activeSocial = 'all';
    public function mount(LiveStream $livestream): void
    {
        $this->livestream = $livestream;
        $this->livestream_id = $livestream->id;
        $this->socialTabs = Social::withCount([
            'auctions as auctions_count' => function ($query) {
                $query->where('live_video_id', $this->livestream_id);
            }
        ])->get()->toArray();
    }

    public function table(Table $table): Table
    {
        // $socials = Social::withCount(['auctions' => function ($query) {
        //     $query->where('live_video_id', $this->livestream_id);
        // }])->get();

        //where('live_video_id',$this->livestream->id)->
        return $table
            ->query(Auction::query())
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->where('live_video_id', $this->livestream_id)
                // ->when(
                //     $this->activeSocial !== 'all',
                //     fn ($query) =>
                //         $query->where('social_id', $this->activeSocial)
                // )
            )
            ->heading('المزايدات')
            ->columns([
                TextColumn::make('marketer.username')
                    ->label('المسوق')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('social.name')
                    ->label('المنصة')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('المشترك')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('السعر')

                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('social_id')
                    ->label('المنصات')
                    ->multiple()
                    ->options(function () {
                        $options = Social::withCount([
                            'auctions as auctions_count' => function ($query) {
                                $query->where('live_video_id', $this->livestream_id);
                            }
                        ])->orderBy('sequence')->get()
                            ->mapWithKeys(fn($social) => [
                                $social->id => "{$social->name} ({$social->auctions_count})"
                            ])->toArray();

                        // إضافة All في الأعلى
                        //return ['all' => 'الكل'] + $options;
                      return  $options;
                    })
                   // ->default(['all'])
                    ->modifyQueryUsing(function (Builder $query, $state) {
                      // \Log::info("state", ["stat" => $state]);
                        // إذا لم يتم اختيار أي شيء → عرض كل السجلات
                        if (empty($state) || empty($state['values']) 
                        //|| in_array('all', $state['values'])
                    ) {
                            return; // تجاهل الفلترة
                        }

                        $ids = array_map('intval', $state['values']);
                        $query->whereIn('social_id', $ids);

                    })
                   // ->isCollapsed(false)
                    ->placeholder('اختر منصة/منصات')
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
        ;
    }

    // private function getAllAuctionsCount(): int
    // {
    //     return Auction::where('live_video_id', $this->livestream_id)->count();
    // }
    // public function setActiveSocial(string|int $socialId): void
    // {
    //     $this->activeSocial = $socialId;

    //     // مهم حتى لا تبقى على صفحة قديمة بعد الفلترة
    //     $this->resetPage();
    // }

    // public function getTabs(): array
// {
//     $statuses = Social::orderBy('sequence')->get();

    // // إنشاء التبويب الرئيسي "All"
// $tabs = [
// 'All' => Tab::make()
//     ->badge(Auction::where('live_video_id', $this->livestream_id)->count())
// ];

    // // إضافة التبويبات الديناميكية لكل حالة
// $dynamicTabs = $statuses->map(function (Social $social) {
// return [
//     $social->name => Tab::make()
//         ->label( $social->name ) // استخدام الترجمة إن وجدت
//         ->modifyQueryUsing(fn (Builder $query) => $query->where('social_id', $social->id))
//         ->badge(Auction::query()->where('social_id', $social->id)->count())
// ];
// })->collapse()->toArray();

    // // دمج التبويب الرئيسي مع التبويبات الديناميكية
// return array_merge($tabs, $dynamicTabs);

    // }
}
