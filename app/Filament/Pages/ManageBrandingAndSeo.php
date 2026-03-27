<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected string $view = 'filament.pages.manage-branding-and-seo';

    protected static string|UnitEnum|null $navigationGroup = 'CMS & Marketing';

    protected static ?string $title = 'Branding & SEO';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::where('group', 'branding_seo')->get();
        if ($settings->isEmpty()) {
            // Fallback to landing_page group for initial migration if they exist there
            $settings = Setting::whereIn('key', ['site_title', 'site_keywords', 'site_description', 'site_logo', 'site_favicon'])->get();
        }
        $this->form->fill($settings->pluck('value', 'key')->toArray());
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
                        TextInput::make('site_keywords')
                            ->label('Keywords')
                            ->placeholder('restaurant, heritage, cuisine, fine dining'),
                        Textarea::make('site_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Brand Assets')
                    ->description('Upload your website logo and favicon.')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Website Logo')
                            ->image()
                            ->directory('branding')
                            ->imagePreviewHeight('100')
                            ->downloadable(),
                        FileUpload::make('site_favicon')
                            ->label('Favicon')
                            ->image()
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
            Setting::setValue($key, $value, 'branding_seo');
        }

        Notification::make()
            ->title('Branding & SEO settings saved successfully')
            ->success()
            ->send();
    }
}
