<?php

namespace App\Livewire\LiveStream;

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
class LiveStreamsTable extends Component  implements  HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;
    public function render()
    {
        return view('livewire.live-stream.live-streams-table');
    }

    public static function table(Table $table): Table
    {
        return $table->query(LiveStream::query())
            ->columns([
                TextColumn::make('marketer.username')
                ->label('المسوق')
                ->searchable()
                    ->sortable(),                
                IconColumn::make('is_active')   
                ->label('البث الآن')             
                    ->boolean(),               
                    TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),                
                    TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->dateTime()
                    ->sortable()
                    ->searchable(), 
                    TextColumn::make('live_duration')
                    ->label('مدة البث')
                  
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                        //   ->orderBy('start_date', $direction)
                            ->orderByRaw('
                            CASE 
                                WHEN end_date IS NOT NULL 
                                THEN TIMESTAMPDIFF(SECOND, start_date, end_date)
                                ELSE TIMESTAMPDIFF(SECOND, start_date, NOW())
                            END '.$direction.'' )
                            ;
                    })
                    // ->getStateUsing(fn ($record) => $record->live_duration)
                    ,
                        
                                 
            ])->defaultSort('start_date', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('details')
               -> label('تفاصيل')
                ->url(fn (): string => route('filament.admin.pages.livestreams'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

}
