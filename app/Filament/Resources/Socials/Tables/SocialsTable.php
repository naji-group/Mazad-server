<?php

namespace App\Filament\Resources\Socials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\HtmlString;
class SocialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->recordUrl(false)
            ->columns([
                TextColumn::make('ar_name')
                ->label('وسيلة التواصل')
                    ->searchable(),
                TextColumn::make('name')
                ->label('الرمز')
               
                    ->searchable(),
              
                TextColumn::make('link')
                ->label('عنوان الحساب')             
                    ->searchable()
                    ->url(function ($record) {
                        return   $record->link;
                   
                    })
                    ->openUrlInNewTab()
                  ,
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
            ]);
    }
}
