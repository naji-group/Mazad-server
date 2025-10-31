<?php

namespace App\Filament\Resources\ExtraSocials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\HtmlString;
use App\Models\Social;
class ExtraSocialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->query(Social::query()->where('is_extra',true))
        ->recordUrl(false)
            ->columns([
                TextColumn::make('name')
                ->label('وسيلة التواصل')
                    ->searchable()   ->disabledClick()
                    ,
                TextColumn::make('code')
                ->label('الرمز')
               
                    ->searchable()
                    ->disabledClick(),
              
               
                    IconColumn::make('is_active')
                    ->label('الحالة')
                    ->sortable()
                    ->boolean(), 
              
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]) 
               ->reorderable('sequence')
            ;
    }
}
