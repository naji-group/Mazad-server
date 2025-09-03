<?php

namespace App\Filament\Resources\Marketers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use App\Models\Marketer;
use Filament\Forms\Components\FileUpload;
use App\Filament\Forms\Components\ImageWithPreview;
class MarketerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('first_name')
                //     ->default(null),
                // TextInput::make('last_name')
                //     ->default(null),
                Grid::make()
                ->schema([              
                TextInput::make('username')
                ->label('اسم المستخدم')
                   // ->default(null)
                    ->required()
                    ->unique(modifyRuleUsing: function ($rule) {
                        return $rule->where('is_active', 1);
                    })
                    ->maxLength(100)
                    ->columnSpan(2),
                    TextInput::make('email')
                    ->label('الايميل')
                    ->email()
                    ->maxLength(100)
                    //->filter(fn (string $value): ?string => filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null) // Enforces a valid domain extension
                    ->nullable()
                    ->unique(modifyRuleUsing: function ($rule) {
                        return $rule->where('is_active', 1);
                    })
                   ->regex('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/')
                       // ->default(null)
                     //  ->disabled()
                       //  ->hiddenOn('create')
                        ->columnSpan(2),
                TextInput::make('password')
                ->label('كلمة المرور')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required((fn (string $operation): bool => $operation === 'create'))
                    ->revealable()
                    ->maxLength(10)
                    ->columnSpan(2)                    ,
                    TextInput::make('password_confirmation')
                    ->label('تأكيد كلمة المرور')                    
                    //->hiddenOn('edit')
                    ->password()
                    ->maxLength(10)
                    ->revealable()
                    ->columnSpan(2),
                      Toggle::make('is_active')
                ->label('مفعل')
                    
                    ->default(null)->columnSpan(2),
                    ImageWithPreview::make('current_image_preview')
                    ->imageUrl(fn(Marketer $record) => $record->image )
                    ->label('الصورة')
                    // ->imageUrl($this->post?->getFirstMediaUrl( $this->media_folder) )
                   // ->altText('Current Image')
                    ->imageHeight(120)
                   ->hiddenOn('create') 
                    // ->live()
                    ->nullable(),
                    FileUpload::make('local_image')
                    ->image()
                    ->disk('public')
                    ->directory('images/marketers')
                    ->visibility('public')
                  -> previewable(),
                // TextInput::make('email')
                //     ->label('Email address')
                //     ->email()
                //     ->default(null),
                // TextInput::make('avatar')
                //     ->default(null),
                // TextInput::make('provider')
                //     ->default(null),
                // TextInput::make('provider_user_id')
                //     ->default(null),
                // Select::make('social_id')
                //     ->relationship('social', 'name')
                //     ->default(null),
                // DateTimePicker::make('email_verified_at'),
                ])->columnSpan(4)
                ->columns(4)
            ]) ;
    }
}
