<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static string $view = 'filament.pages.manage-general-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public function mount(): void
    {
        $this->form->fill(app(GeneralSettings::class)->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Site')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->required(),
                        Forms\Components\TextInput::make('site_tagline'),
                        Forms\Components\DateTimePicker::make('countdown_target_at'),
                    ]),
                Forms\Components\Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('contact_email')
                            ->email(),
                        Forms\Components\TextInput::make('contact_phone'),
                        Forms\Components\Textarea::make('contact_address')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Social Links')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('social_facebook')->url(),
                        Forms\Components\TextInput::make('social_twitter')->url(),
                        Forms\Components\TextInput::make('social_linkedin')->url(),
                        Forms\Components\TextInput::make('social_youtube')->url(),
                        Forms\Components\TextInput::make('social_instagram')->url(),
                    ]),
                Forms\Components\Section::make('SEO & Analytics')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('ga_measurement_id')
                            ->label('Google Analytics Measurement ID')
                            ->placeholder('G-XXXXXXXXXX'),
                        Forms\Components\TextInput::make('gtm_container_id')
                            ->label('Google Tag Manager Container ID')
                            ->placeholder('GTM-XXXXXXX'),
                        Forms\Components\FileUpload::make('default_og_image')
                            ->label('Default Social Share Image')
                            ->image()
                            ->directory('seo')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Other')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('sms_enabled')
                            ->label('Enable SMS notifications'),
                        Forms\Components\Textarea::make('social_wall_embed_code')
                            ->label('Social Wall Embed Code')
                            ->helperText('Paste an embed snippet (e.g. from a social wall aggregator) to show on the homepage.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->fill($this->form->getState());
        $settings->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
