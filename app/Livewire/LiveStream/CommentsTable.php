<?php

namespace App\Livewire\LiveStream;

use App\Models\Auction;
use App\Models\LiveStream;
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
use Filament\Actions\Action;

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
    public function mount(LiveStream $livestream): void
    {
        $this->livestream = $livestream;
       $this->livestream_id= $livestream->id;
    }

    public   function table(Table $table): Table
    {

        //where('live_video_id',$this->livestream->id)->
        return $table ->query(Auction::query())
        ->modifyQueryUsing(fn (Builder $query) =>
            $query->where('live_video_id', $this->livestream_id)
        )
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
                // TextColumn::make('live_duration')
                // ->label('مدة البث')

                // ->sortable(query: function (Builder $query, string $direction): Builder {
                //     return $query
                //     //   ->orderBy('start_date', $direction)
                //         ->orderByRaw('
                //         CASE 
                //             WHEN end_date IS NOT NULL 
                //             THEN TIMESTAMPDIFF(SECOND, start_date, end_date)
                //             ELSE TIMESTAMPDIFF(SECOND, start_date, NOW())
                //         END '.$direction.'' )
                //         ;
                // })
                // ->getStateUsing(fn ($record) => $record->live_duration)



            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
              //  ViewAction::make(),
                EditAction::make(),
             
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

}
