<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
 

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label('الاسم')
                    ->required(),
                TextInput::make('email')
                    ->label('الايميل')
                    ->email()
                    ->required(),
                    Select::make('role')
                    ->label('الصفة')
                    ->options([
                        'admin' => 'مدير',
                        'supervisor' => 'مشرف',
                       
                    ])
                    ->required(),
                    TextInput::make('password')
                ->label('كلمة المرور')
                    ->password()->confirmed()
                    ->hiddenOn('edit')
                    ->revealable()
                     ,
                    TextInput::make('password_confirmation')
                    ->hiddenOn('edit')
                    ->password(),
                    FileUpload::make('image')
    ->image()
    ->disk('public')
    ->directory('users')
    ->visibility('public')
  -> previewable(),
            
               
            ]);
    }
}
