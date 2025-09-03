<?php

namespace App\Filament\Resources\Marketers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Icons\Heroicon;
class MarketersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('first_name')
                //     ->searchable(),
                // TextColumn::make('last_name')
                //     ->searchable(),
                TextColumn::make('username')
                ->label('اسم المستخدم')
                    ->searchable(),        
                    TextColumn::make('email')
                ->label('الايميل')                    
                    ->searchable(),
                        IconColumn::make('is_active')
                        ->label('الحالة')
                        ->sortable()
                        ->boolean(),                   
             
                // TextColumn::make('avatar')
                //     ->searchable(),
                // TextColumn::make('provider')
                //     ->searchable(),
                // TextColumn::make('provider_user_id')
                //     ->searchable(),
                // TextColumn::make('social.name')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('email_verified_at')
                //     ->dateTime()
                //     ->sortable(),
                TextColumn::make('created_at')
                ->label('تاريخ الانشاء')
                    ->dateTime()
                    ->sortable()
                   // ->toggleable(isToggledHiddenByDefault: true)
                    ,
              
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ])
     
            ->toolbarActions([              
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
