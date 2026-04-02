<?php

namespace App\Livewire\Frontend;

use App\Models\Setting;
use App\Services\MetaService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layout')]
class Home extends Component
{
    public function render()
    {
        $settings = Setting::where('group', 'landing_page')->pluck('value', 'key');

        // Convert lp_ prefixed keys to match view variable expectations
        $cms = (object) $settings->mapWithKeys(function ($value, $key) {
            return [str_replace('lp_', '', $key) => $value];
        })->all();

        // Server-side Meta CAPI Tracking
        try {
            app(MetaService::class)->sendEvent('PageView');
        } catch (\Exception $e) {
            // Ignore if service fails
        }

        return view('welcome', compact('cms'));
    }
}
