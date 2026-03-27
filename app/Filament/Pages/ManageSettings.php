<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
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

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.manage-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'General Settings';

    protected static ?string $slug = 'settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('group', ['marketing', 'general'])->get();
        $this->form->fill($settings->pluck('value', 'key')->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Marketing (Facebook Meta)')
                    ->description('Configuration for Facebook Pixel and Conversions API.')
                    ->schema([
                        TextInput::make('fb_pixel_id')
                            ->label('Facebook Pixel ID')
                            ->placeholder('e.g. 1234567890'),
                        TextInput::make('fb_capi_token')
                            ->label('Conversions API Access Token')
                            ->password()
                            ->revealable(),
                        TextInput::make('fb_test_event_code')
                            ->label('Test Event Code')
                            ->helperText('Only needed for testing server-side events.'),
                    ])->columns(2),

                Section::make('General Configuration')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->default(config('app.name')),
                        // Add more general settings here as needed
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Determine group based on key prefix or mapping
            $group = 'general';
            if (str_starts_with($key, 'fb_')) {
                $group = 'marketing';
            }

            Setting::setValue($key, $value, $group);
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
