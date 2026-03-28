<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Table;
use Livewire\Attributes\Layout;
use Livewire\Component;

class TableMenu extends Component
{
    public Table $table;

    public string $search = '';

    #[Layout('layouts.qr-menu')]
    public function render()
    {
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('is_active', true)
                ->when($this->search, function ($q) {
                    $q->where(fn ($sub) => $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%"));
                });
        }])
            ->whereHas('menuItems', function ($query) {
                $query->where('is_active', true)
                    ->when($this->search, function ($q) {
                        $q->where(fn ($sub) => $sub->where('name', 'like', "%{$this->search}%")
                            ->orWhere('description', 'like', "%{$this->search}%"));
                    });
            })
            ->orderBy('priority_order')
            ->get();

        return view('livewire.table-menu', [
            'categories' => $categories,
            'site_name' => Setting::getValue('site_name', config('app.name')),
            'hero_title' => Setting::getValue('qr_menu_hero_title', 'The Art of <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-200">Fine Dining</span>'),
            'hero_subtitle' => Setting::getValue('qr_menu_hero_subtitle', 'Explore our hand-crafted menu designed for your exquisite taste.'),
            'badge_text' => Setting::getValue('qr_menu_badge_text', 'Chef\'s Recommendation'),
            'footer_text' => Setting::getValue('qr_menu_footer_text', 'Powered by Antigravity OS'),
        ]);
    }
}
