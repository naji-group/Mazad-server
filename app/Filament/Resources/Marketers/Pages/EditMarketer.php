<?php

namespace App\Filament\Resources\Marketers\Pages;

use App\Filament\Resources\Marketers\MarketerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\MarketerSocial;
// use App\Models\Marketer;
// use Filament\Forms;
class EditMarketer extends EditRecord
{
    protected static string $resource = MarketerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
       
        ];
    }
    protected function getFormActions(): array
    {
        return [
         ...parent::getFormActions(), // زر الحفظ الأساسي

          
            // Action::make('saveSocials')
           
            //     ->label('💾 حفظ الحسابات الاجتماعية')
            //     ->color('success')
            //     ->action(function () {
            //         $data = $this->form->getState();
            //         $record = $this->record;

            //         foreach ($data['socials'] ?? [] as $socialId => $values) {
            //             MarketerSocial::updateOrCreate(
            //                 [
            //                     'marketer_id' => $record->id,
            //                     'social_id'   => $socialId,
            //                 ],
            //                 [
            //                     'link'      => $values['link'] ?? null,
            //                     'is_active' => $values['is_active'] ?? 0,
            //                 ]
            //             );
            //         }

            //        Notification::make()
            //             ->title('تم حفظ الحسابات الاجتماعية بنجاح ✅')
            //             ->success()
            //             ->send();
            //     }),
        ];
    }

    
//     public function saveSocials($data): void
//     {
//         //$data = $this->form->getState();
// dd($data );
//         foreach ($data['socials'] ?? [] as $socialId => $values) {
//             MarketerSocial::updateOrCreate(
//                 [
//                     'marketer_id' => $this->record->id,
//                     'social_id'   => $socialId,
//                 ],
//                 [
//                     'link'      => $values['link'] ?? null,
//                     'is_active' => $values['is_active'] ?? 0,
//                 ]
//             );
//         }

//         \Filament\Notifications\Notification::make()
//             ->title('✅ تم حفظ الحسابات الاجتماعية بنجاح')
//             ->success()
//             ->send();
//     }

    // public function getFooter(): ?\Illuminate\Contracts\View\View
    // {
    //     return view('livewire.marketer.marketer-social-form', [
    //         'marketer' => $this->record,
    //     ]);
    // }

//     protected function afterSave(): void
// {
//     $data = $this->form->getState();
// dd($data);
//     foreach ($data['socials'] ?? [] as $socialId => $values) {
//         MarketerSocial::updateOrCreate(
//             [
//                 'marketer_id' => $this->record->id,
//                 'social_id'   => $socialId,
//             ],
//             [
//                 'link'      => $values['link'] ?? null,
//                 'is_active' => $values['is_active'] ?? 0,
//             ]
//         );
//     }
//     \Filament\Notifications\Notification::make()
//     ->title('✅ تم حفظ الحسابات الاجتماعية بنجاح')
//     ->success()
//     ->send();
// }
 
// protected function mutateFormDataBeforeSave(array $data): array
// {
    
//     \Log::debug('pathImg', [
                 
//         'pathImg' => $data  ,
//     ]);
//     return $data;
// }


protected function afterSave(): void
{
    // جرب تسجيل الحالة للتأكد أن الكود يُنفَّذ
    $data = $this->form->getState();
   // Log::debug('EditMarketer::afterSave form state', ['data' => $data]);
    \Log::debug('data', [
                 
        'data' => $data  ,
    ]);
    // الآن قم بمعالجة بيانات socials إن وُجدت
    foreach ($data['socials'] ?? [] as $socialId => $values) {
        MarketerSocial::updateOrCreate(
            [
                'marketer_id' => $this->record->id,
                'social_id'   => $socialId,
            ],
            [
                'link'      => $values['link'] ?? null,
                'is_active' => $values['is_active'] ?? 0,
            ]
        );
    }

    // Notification::make()
    //     ->title('تم حفظ البيانات بنجاح')
    //     ->success()
    //     ->send();
}
 
}
