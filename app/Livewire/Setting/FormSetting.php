<?php

namespace App\Livewire\Setting;


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

use Illuminate\Support\Facades\Storage;
use File;
// use Illuminate\Support\Carbon;
// use Intervention\Image\ImageManager;
// use Intervention\Image\Drivers\Gd\Driver;
class FormSetting extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Setting $setting;
     
    public array $data = [];
    public function mount(Setting $setting)
    {
        $this->setting = $setting;  
        $this->data = [
            'value' => $this->setting->value,
            'image' => null,
        ];

    }
    protected function schema(Schema $schema): Schema
    {
        $imagegrid = Grid::make()
            ->schema([
                FileUpload::make('data.image')
                    ->label('الصورة')
                    //->image()
                    ->previewable(false)
                    ->disk('public')
                    ->directory($this->setting->dir)
                    ->visibility('public')
                    ->multiple(false),
    
                ImageWithPreview::make('local_image_preview')
                     ->imageUrl(config('filesystems.disks.public.url') . '/' . $this->setting->image)
                    ->label('الصورة الحالية')
                    ->altText('الصورة الحالية')
                    ->imageHeight(120)
                    ->nullable(),
            ])
            ->columns(2)
            ->visible(fn(): bool => $this->setting->has_image);
    
        return $schema->components([
            Section::make($this->setting->name)
                ->schema([
                    TextInput::make("data.value")
                        ->label($this->setting->name)
                        ->required()
                       ->visible(fn(): bool => ($this->setting->code !== 'default-marketer')&&($this->setting->code !== 'default-social'))
    ,
                    $imagegrid,
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
                if (!empty($this->data['image'])  ) {
                    $this->storeImage($this->data['image']);
                } 
                $this->schema->getState();  //for validation    
                if(isset($this->data['value']))   {
                    $this->setting->value=$this->data['value'];
                }   
                
                $this->setting->save();
                \Filament\Notifications\Notification::make()
            ->title('تم الحفظ')
            ->success()
            ->send();
    }
    public function storeImage($fileArr)
    {
        $file = reset($fileArr);
        $oldimage = $this->setting->image;       
        $path = $this->setting->dir;
       
        //save photo

        if ($file !== null) {
           // $filename = rand(10000, 99999) . $this->setting->id . ".webp";
            $filename = rand(10000, 99999) . $this->setting->id . '.'.$file->getClientOriginalExtension();
            // $manager = new ImageManager(new Driver());
            // $image = $manager->read($file);
           // $image = $image->toWebp(75);
            if (!File::isDirectory(Storage::url('/' . $path))) {
                Storage::makeDirectory('public/' . $path);
            }
            $newpath = $path . '/' . $filename;
          
        
           
           // $path = $file->storeAs(storage_path('app/public') .'/' . $path,$filename);
         $path = $file->storeAs($this->setting->dir, $filename, 'public');
                       $pathImg = storage_path('app/public/' . $oldimage);
            if (File::exists($pathImg)) {
                File::delete($pathImg);
            }
            $tempPath = $file->getRealPath();

            if (File::exists($tempPath)) {
                File::delete($tempPath);
            }
             

        }
       $this->setting->image = $newpath;
       $this->data['image'] = null;
      //  $this->setting->save();
        //     $filePath = storage_path('app/public/' .$newpath);
        //    $strgCtrlr->changemod($filePath);
        return 1;
    }
    public function render()
    {
        return view('livewire.setting.form-setting');
    }
}
