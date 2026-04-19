<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected string $view = 'filament.pages.manage-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Setup';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'General Settings';

    protected static ?string $slug = 'settings';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateSitemap')
                ->label('Generate Sitemap')
                ->icon('heroicon-o-globe-alt')
                ->color('info')
                ->action(function () {
                    Artisan::call('seo:generate-sitemap');

                    Notification::make()
                        ->title('Sitemap generated successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public ?array $data = [];

    /** Setting keys that are stored as boolean flags. */
    protected array $booleanSettings = [
        'pos_auto_print_receipt',
        // Payment gateway toggles
        'payment_bkash_enabled',
        'payment_bkash_sandbox',
        'payment_sslcommerze_enabled',
        'payment_sslcommerze_sandbox',
    ];

    /** Setting keys that are stored as JSON arrays. */
    protected array $jsonSettings = [
        'footer_social_links',
        'footer_opening_hours',
    ];

    public function mount(): void
    {
        $settings = Setting::where('group', 'general')->get()->pluck('value', 'key')->toArray();

        // Cast boolean settings so Filament Toggle renders correctly.
        foreach ($this->booleanSettings as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = (bool) $settings[$key];
            }
        }

        // Decode JSON settings so Repeaters can render them.
        foreach ($this->jsonSettings as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = json_decode($settings[$key], true) ?: [];
            }
        }

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General Configuration')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->default(config('app.name')),
                    ])->columns(2),

                Section::make('Footer Information')
                    ->schema([
                        Textarea::make('footer_about_text')
                            ->label('About Brand')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('footer_address')
                            ->label('Address')
                            ->required()
                            ->placeholder('Banani Rd 11, Block H, Dhaka'),
                        TextInput::make('footer_phone')
                            ->label('Inquiry Phone')
                            ->tel()
                            ->required()
                            ->placeholder('+880 1234 567890'),
                    ])->columns(2),

                Section::make('Availability (Opening Hours)')
                    ->description('Add multiple rows for different day ranges or seasons.')
                    ->schema([
                        Repeater::make('footer_opening_hours')
                            ->label('Opening Hours')
                            ->schema([
                                TextInput::make('days')
                                    ->placeholder('e.g. Mon - Thu')
                                    ->required(),
                                TextInput::make('hours')
                                    ->placeholder('e.g. 12pm - 11pm')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn ($state) => $state['days'] ?? 'New Time Slot'),
                    ]),

                Section::make('Social Media Presence')
                    ->description('Add links to your social media profiles.')
                    ->schema([
                        Repeater::make('footer_social_links')
                            ->label('Social Links')
                            ->schema([
                                Select::make('platform')
                                    ->options([
                                        'facebook' => 'Facebook',
                                        'instagram' => 'Instagram',
                                        'whatsapp' => 'WhatsApp',
                                        'twitter' => 'Twitter/X',
                                        'youtube' => 'YouTube',
                                        'linkedin' => 'LinkedIn',
                                    ])
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn ($state) => ucfirst($state['platform'] ?? 'New Social Link')),
                    ]),

                Section::make('POS Settings')
                    ->icon('heroicon-o-calculator')
                    ->description('Configure Point of Sale receipt and printing behaviour.')
                    ->schema([
                        Toggle::make('pos_auto_print_receipt')
                            ->label('Auto-Print Receipt After Order')
                            ->helperText('When enabled, a thermal receipt window will open automatically after each order is confirmed.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Section::make('Payment Gateways')
                    ->icon('heroicon-o-credit-card')
                    ->description('Configure online payment methods for the POS.')
                    ->schema([
                        // bKash Section
                        Toggle::make('payment_bkash_enabled')
                            ->label('Enable bKash')
                            ->helperText('Allow customers to pay with bKash')
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpanFull(),

                        Toggle::make('payment_bkash_sandbox')
                            ->label('bKash Sandbox Mode')
                            ->helperText('Use bKash sandbox for testing. Disable for production.')
                            ->onColor('warning')
                            ->offColor('gray')
                            ->visible(fn ($get) => $get('payment_bkash_enabled')),

                        TextInput::make('payment_bkash_store_username')
                            ->label('Store Username')
                            ->placeholder('Enter bKash store username')
                            ->helperText('From bKash developer portal')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('payment_bkash_enabled')),

                        TextInput::make('payment_bkash_store_password')
                            ->label('Store Password')
                            ->placeholder('Enter bKash store password')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('payment_bkash_enabled')),

                        TextInput::make('payment_bkash_app_key')
                            ->label('App Key')
                            ->placeholder('Enter bKash app key')
                            ->helperText('From bKash developer portal')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('payment_bkash_enabled')),

                        // SSLCommerze Section
                        Toggle::make('payment_sslcommerze_enabled')
                            ->label('Enable SSLCommerze')
                            ->helperText('Allow customers to pay via SSLCommerze')
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpanFull(),

                        Toggle::make('payment_sslcommerze_sandbox')
                            ->label('SSLCommerze Sandbox Mode')
                            ->helperText('Use SSLCommerze sandbox for testing. Disable for production.')
                            ->onColor('warning')
                            ->offColor('gray')
                            ->visible(fn ($get) => $get('payment_sslcommerze_enabled')),

                        TextInput::make('payment_sslcommerze_store_id')
                            ->label('Store ID')
                            ->placeholder('Enter SSLCommerze store ID')
                            ->helperText('From SSLCommerze dashboard')
                            ->visible(fn ($get) => $get('payment_sslcommerze_enabled')),

                        TextInput::make('payment_sslcommerze_store_password')
                            ->label('Store Password')
                            ->placeholder('Enter SSLCommerze store password')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('payment_sslcommerze_enabled')),

                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Normalize boolean toggle values to '1'/'0' strings for consistent DB storage.
            if (in_array($key, $this->booleanSettings, true)) {
                $value = $value ? '1' : '0';
            }

            // Encode JSON settings for DB storage.
            if (in_array($key, $this->jsonSettings, true)) {
                $value = json_encode($value);
            }

            Setting::setValue($key, $value, 'general');
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
