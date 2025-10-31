<?php

namespace App\Filament\Resources\Socials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\HtmlString;
use App\Models\Social;
class SocialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
      ->query(Social::query()->where('is_extra',null))
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
                DeleteAction::make()
                // ->action(function ($record,DeleteAction $action) {
                //     try {
                //         // حفظ الصفحة الحالية
                //    //     $currentPage = app('livewire')->get('table')->getTablePage();
                //       //  dd($record->id.'-'.$record->code);
                //         // حذف السجل
                //         $record->delete();
                        
                //         // إعادة ضبط الباجينيشن بعد الحذف
                //         // $totalRecords = Social::count();
                //         // $perPage = app('livewire')->get('table')->getTableRecordsPerPage();
                //         // $totalPages = ceil($totalRecords / $perPage);
                        
                //         // if ($currentPage > $totalPages && $totalPages > 0) {
                //         //     app('livewire')->get('table')->setTablePage($totalPages);
                //         // }
                        
                //         // // إعادة تحميل البيانات
                //         // app('livewire')->get('table')->resetTable();
                        
                //     } catch (\Exception $e) {
                //         $action->failureNotificationTitle('فشل في الحذف: ' . $e->getMessage());
                //         $action->failure();
                //     }
                // })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                 //   DeleteBulkAction::make(),
                ]),
            ]) 
               ->reorderable('sequence')
            ;
    }
}
