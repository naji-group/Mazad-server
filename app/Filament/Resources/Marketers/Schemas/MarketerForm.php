<?php

namespace App\Filament\Resources\Marketers\Schemas;

 use Filament\Actions\EditAction;
// use Filament\Forms\Components\DateTimePicker;
// use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\MaxWidth;
use App\Models\Marketer;
use App\Models\MarketerSocial;
use Filament\Forms\Components\FileUpload;
use App\Filament\Forms\Components\ImageWithPreview;
use App\Http\Controllers\Api\HelpController;

use Filament\Actions\Action;
use App\Models\Social;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
//use App\Livewire\Marketer\MarketerSocialForm;
// use App\Filament\Resources\Marketers\Components\MarketerSocialForm;
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
                            ->required()
                            ->unique(modifyRuleUsing: function ($rule) {
                                return $rule->where('is_active', 1);
                            })
                            ->regex('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/')
                            // ->default(null)
                            //  ->disabled()
                            //  ->hiddenOn('create')
                            ->columnSpan(2),
                        TextInput::make('full_name')
                            ->label('الاسم الكامل')
                             ->default(null)
                            //->required()
                            ->hiddenOn('create')
                            ->maxLength(1000)
                            ->columnSpan(2),
                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required((fn(string $operation): bool => $operation === 'create'))
                            ->revealable()
                            ->maxLength(10)
                            ->columnSpan(2),
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
                            ->imageUrl(fn(Marketer $record) => $record->image)
                            ->label('الصورة')
                            // ->imageUrl($this->post?->getFirstMediaUrl( $this->media_folder) )
                            // ->altText('Current Image')
                            ->imageHeight(120)
                            ->hiddenOn('create')
                            // ->live()
                            ->nullable(),
                        ImageWithPreview::make('local_image_preview')
                            ->imageUrl(fn(Marketer $record) => config('filesystems.disks.public.url') . '/' . $record->local_image)
                            ->label('الصورة الحالية')
                            // ->imageUrl($this->post?->getFirstMediaUrl( $this->media_folder) )
                            // ->altText('Current Image')
                            ->imageHeight(120)
                            ->hiddenOn('create')
                            // ->live()
                            ->nullable(),
                        FileUpload::make('local_image')
                            ->label('صورة')
                            ->image()
                            ->previewable(false)
                            // ->fetchFileInformation(false)
                            ->disk('public')
                            ->directory('images/marketers')
                            ->visibility('public')
                        // -> previewable()
                        ,
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
                    ->columns(4),
                //    ...MarketerSocialForm::schema($schema->getRecord()),
                // Section::make('الحسابات الاجتماعية')
                // ->hiddenOn('create')
                // ->schema(fn (?Marketer $record) => MarketerSocialForm::schema($record)),

                //  MarketerSocialForm::schema(function (Marketer $record=null) {return $record;})
                Section::make('الحسابات الاجتماعية')->hiddenOn('create')
                    ->schema(
                        function (Marketer $record = null) {
                            $fields = [];
                            if ($record) {
                                foreach (Social::with([
                                    'marketersocials' => function ($q) use ($record) {
                                        $q->where('marketer_id', $record->id)
                                        ;
                                    }
                                ])->where('is_extra',null)->orWhere('is_extra',false)->orderBy('sequence')->get() as $social) {
                                    $fields[] = Group::make([
                                        TextInput::make("socials.{$social->id}.link")
                                            ->label("رابط {$social->name}")
                                            ->hiddenOn('create')
                                            ->afterStateHydrated(function ($component, $state, $record) use ($social) {
                                                if ($record) {
                                                    $value = $social->marketersocials?->first()?->link
                                                        // ->where('social_id', $social->id)
                                                        //  ->first()?->link;
                                                    ;
                                                    $component->state($value);
                                                }
                                            })->columnSpan(3),
                                        Toggle::make("socials.{$social->id}.is_active")
                                            ->label("تفعيل {$social->name}")
                                            ->hiddenOn('create')
                                            ->afterStateHydrated(function ($component, $state, $record) use ($social) {
                                                if ($record) {
                                                    $value = $social->marketersocials?->first()?->is_active ?? false;

                                                    $component->state($value);
                                                }
                                            })
                                             ->columnSpan(1)
                                            ,

                                    ])->columnSpanFull()->columns(4);
                                }

                                return $fields;
                            }

                        },


                    )->columnSpanFull()->columns(4),
                    // EditAction::make()
                    // ->mutateDataUsing(function (array $data): array {
                    //     \Log::debug('pathImg', [
                 
                    //         'pathImg' => $data  ,
                    //     ]);
                
                    //     return $data;
                    // })

            ]) ;
    }
}
