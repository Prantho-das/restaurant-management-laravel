<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected string $view = 'filament.pages.manage-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'General Settings';

    protected static ?string $slug = 'settings';

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

    public function mount(): void
    {
        $settings = Setting::where('group', 'general')->get()->pluck('value', 'key')->toArray();

        // Cast boolean settings so Filament Toggle renders correctly.
        foreach ($this->booleanSettings as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = (bool) $settings[$key];
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

                Section::make('QR Menu Visuals')
                    ->description('Customize the visual story and branding of your digital QR menu.')
                    ->schema([
                        TextInput::make('qr_menu_hero_title')
                            ->label('Hero Title')
                            ->placeholder('e.g., The Art of Fine Dining')
                            ->helperText('Use <br> for line breaks. You can use Tailwind classes inside the title.'),
                        TextInput::make('qr_menu_hero_subtitle')
                            ->label('Hero Subtitle')
                            ->placeholder('e.g., Explore our hand-crafted menu designed for your exquisite taste.'),
                        TextInput::make('qr_menu_badge_text')
                            ->label('Hero Badge Text')
                            ->placeholder('e.g., Chef\'s Recommendation'),
                        TextInput::make('qr_menu_footer_text')
                            ->label('Footer Text')
                            ->placeholder('e.g., Powered by Antigravity OS'),
                    ])->columns(2),

                Section::make('Footer Information')
                    ->schema([
                        Textarea::make('footer_about_text')
                            ->label('About Brand')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact & Hours')
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
                        TextInput::make('footer_hours_mon_thu')
                            ->label('Mon - Thu Hours')
                            ->placeholder('12pm - 11pm'),
                        TextInput::make('footer_hours_fri_sun')
                            ->label('Fri - Sun Hours')
                            ->placeholder('2pm - 12am'),
                    ])->columns(2),

                Section::make('Social Media Links')
                    ->schema([
                        TextInput::make('social_instagram_url')
                            ->label('Instagram URL')
                            ->url()
                            ->placeholder('https://instagram.com/royaldine'),
                        TextInput::make('social_facebook_url')
                            ->label('Facebook URL')
                            ->url()
                            ->placeholder('https://facebook.com/royaldine'),
                    ])->columns(2),

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

            Setting::setValue($key, $value, 'general');
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
