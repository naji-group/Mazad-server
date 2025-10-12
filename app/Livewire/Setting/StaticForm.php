<?php

namespace App\Livewire\Setting;

use Filament\Forms\Components\Textarea;
use Livewire\Component;

use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use App\Filament\Forms\Components\ImageWithPreview;
use App\Models\Setting;
use Filament\Actions\Action;
class StaticForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Setting $setting;
     
    public array $data = [];
    public function mount(Setting $setting)
    {
        $this->setting = $setting;  
        $this->data = [
            'name' => $this->setting->name,
            'value' => $this->setting->value,
                      
            'code' =>$this->setting->code,
        ];

    }
    protected function schema(Schema $schema): Schema
    {
        
        return $schema->components([
            Section::make($this->setting->name)
                ->schema([
                    TextInput::make("data.name")                    
                        ->label('عنوان الصفحة')  
                                              
                        ->required(),
                        TextInput::make("data.code")     
                                  
                        ->label('الرابط الفرعي')  
                      //  ->prefix(url('/'))   
                       // ->extraInputAttributes(['style' => 'text-align: left; direction:ltr !important;']) // For righ                   
                        ->required(),
                        Textarea::make("data.value")                    
                        ->label('المحتوى') 
                        ->rows(10)                       
                        ->required()
                        ,
                   
                    Action::make('save')
                    ->label('حفظ')
                    ->submit('save')
             
                ]),
        ]);
    }
    

    public function save()
    {
   //  $this->schema->getState();
    //  \Log::debug('pathImg', [
                 
    //     'pathImg' => $data  ,
    // ]);
                
                $this->schema->getState();  //for validation    
                if(isset($this->data['value']))   {
                    $this->setting->value=$this->data['value'];
                    $this->setting->name=$this->data['name'];
                    $this->setting->code=$this->data['code'];
                }   
                
                $this->setting->save();
                \Filament\Notifications\Notification::make()
            ->title('تم الحفظ')
            ->success()
            ->send();
    }
   
 
    public function render()
    {
        return view('livewire.setting.static-form');
    }
}
