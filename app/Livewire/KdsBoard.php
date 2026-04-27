<?php

namespace App\Livewire;

use App\Models\KotOrder;
use App\Models\KotOrderItem;
use App\Services\KotService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class KdsBoard extends Component
{
    public array $pendingKots = [];
    public array $preparingKots = [];
    public array $readyKots = [];
    public array $stats = [];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $kotService = app(KotService::class);

        $this->pendingKots = KotOrder::with(['order', 'items', 'sentBy'])
            ->where('status', 'pending')
            ->orderBy('sent_at', 'asc')
            ->get()
            ->toArray();

        $this->preparingKots = KotOrder::with(['order', 'items', 'sentBy'])
            ->where('status', 'preparing')
            ->orderBy('sent_at', 'asc')
            ->get()
            ->toArray();

        $this->readyKots = KotOrder::with(['order', 'items', 'sentBy'])
            ->where('status', 'ready')
            ->orderBy('sent_at', 'asc')
            ->get()
            ->toArray();

        $this->stats = $kotService->getKotStats();
    }

    public function updateKotStatus(int $kotId, string $status)
    {
        $kotOrder = KotOrder::find($kotId);

        if (!$kotOrder) {
            return;
        }

        $kotService = app(KotService::class);
        $kotService->updateKotStatus($kotOrder, $status, Auth::id());

        $this->refreshData();
        $this->dispatch('status-updated');
    }

    public function updateItemStatus(int $itemId, string $status)
    {
        $item = KotOrderItem::find($itemId);

        if (!$item) {
            return;
        }

        $kotService = app(KotService::class);
        $kotService->updateItemStatus($item, $status);

        $this->refreshData();
        $this->dispatch('item-updated');
    }

    #[Layout('layouts.kds')]
    public function render()
    {
        return view('livewire.kds-board');
    }
}
