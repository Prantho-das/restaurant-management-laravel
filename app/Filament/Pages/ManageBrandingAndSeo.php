<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageBrandingAndSeo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected string $view = 'filament.pages.manage-branding-and-seo';

    protected static string|UnitEnum|null $navigationGroup = 'Setup';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Branding & SEO';

    public ?array $data = [];

    public function mount(): void
    {
        // Load settings from branding_seo and marketing groups
        $settings = Setting::whereIn('group', ['branding_seo', 'marketing'])->get();
        $formData = $settings->pluck('value', 'key')->toArray();

        // Compatibility check: Also look for qr_menu settings that might still be in 'general' group
        $qrKeys = ['qr_menu_hero_title', 'qr_menu_hero_subtitle', 'qr_menu_badge_text', 'qr_menu_footer_text'];
        foreach ($qrKeys as $key) {
            if (! isset($formData[$key])) {
                $formData[$key] = Setting::getValue($key);
            }
        }

        // Legacy fallback for SEO settings
        $seoKeys = ['site_title', 'site_keywords', 'site_description', 'site_logo', 'site_favicon'];
        foreach ($seoKeys as $key) {
            if (! isset($formData[$key])) {
                $formData[$key] = Setting::getValue($key);
            }
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('SEO Settings')
                    ->description('Manage website metadata for improved search engine visibility.')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Website Title')
                            ->placeholder('Royal Dine - Premium Heritage Cuisine')
                            ->required(),
                        TextInput::make('site_subtitle')
                            ->label('Website Subtitle')
                            ->placeholder('Heritage Cuisine')
                            ->helperText('This tagline appears next to or below your logo/title.'),
                        TextInput::make('site_keywords')
                            ->label('Keywords')
                            ->placeholder('restaurant, heritage, cuisine, fine dining'),
                        Textarea::make('site_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Marketing (Facebook Meta)')
                    ->description('Configuration for Facebook Pixel and Conversions API.')
                    ->schema([
                        TextInput::make('fb_pixel_id')
                            ->label('Facebook Pixel ID')
                            ->numeric()
                            ->placeholder('e.g. 1234567890'),
                        TextInput::make('fb_capi_token')
                            ->label('Conversions API Access Token')
                            ->password()
                            ->revealable(),
                        TextInput::make('fb_test_event_code')
                            ->label('Test Event Code')
                            ->helperText('Only needed for testing server-side events.'),
                    ])->columns(2),

                Section::make('Google Tag Manager')
                    ->description('Configuration for Google Tag Manager.')
                    ->schema([
                        TextInput::make('gtm_id')
                            ->label('GTM Container ID')
                            ->placeholder('e.g. G-XXXXXXXXXX')
                            ->helperText('Enter your GTM container ID (e.g., G-XXXXXXX or GTM-XXXXXX)'),
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

                Section::make('Brand Assets')
                    ->description('Upload your website logo and favicon.')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Website Logo')
                            ->image()
                            ->directory('branding')
                            ->disk('public')
                            ->imagePreviewHeight('100')
                            ->downloadable(),
                        FileUpload::make('site_favicon')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('branding')
                            ->imagePreviewHeight('50')
                            ->downloadable(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $group = str_starts_with($key, 'fb_') ? 'marketing' : 'branding_seo';
            Setting::setValue($key, $value, $group);
        }

        Notification::make()
            ->title('Branding & SEO settings saved successfully')
            ->success()
            ->send();
    }
}
