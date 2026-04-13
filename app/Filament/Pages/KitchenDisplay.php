<?php

namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\KotOrder;
use App\Models\KotOrderItem;
use App\Services\KotService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class KitchenDisplay extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-fire';

    protected static string|UnitEnum|null $navigationGroup = 'Kitchen';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Kitchen Display';

    protected static ?string $title = 'KDS';

    protected string $view = 'filament.pages.kitchen-display';

    public array $pendingKots = [];

    public array $preparingKots = [];

    public array $readyKots = [];

    public array $stats = [];

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
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

    public function updateKotStatus(int $kotId, string $status): void
    {
        $kotOrder = KotOrder::find($kotId);

        if (!$kotOrder) {
            return;
        }

        $kotService = app(KotService::class);
        $kotService->updateKotStatus($kotOrder, $status, Auth::id());

        $this->refreshData();
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        $item = KotOrderItem::find($itemId);

        if (!$item) {
            return;
        }

        $kotService = app(KotService::class);
        $kotService->updateItemStatus($item, $status);

        $this->refreshData();
    }

    public function printKot(int $kotId): void
    {
        $this->dispatch('print-kot', kotId: $kotId);
    }

    public function getListeners(): array
    {
        return [
            'kot-created' => 'refreshData',
            'kot-updated' => 'refreshData',
        ];
    }
}
