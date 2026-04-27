<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    public function created(Purchase $purchase): void
    {
        if ($purchase->status === 'received' && ! $purchase->is_stock_updated) {
            DB::afterCommit(function () use ($purchase) {
                Purchase::handleReceivedStatus($purchase->fresh());
            });
        }
    }

    public function updated(Purchase $purchase): void
    {
        if ($purchase->status === 'received' && ! $purchase->is_stock_updated) {
            DB::afterCommit(function () use ($purchase) {
                Purchase::handleReceivedStatus($purchase->fresh());
            });
        } elseif ($purchase->status !== 'received' && $purchase->getOriginal('status') === 'received' && $purchase->is_stock_updated) {
            DB::afterCommit(function () use ($purchase) {
                $purchase->loadMissing('items.ingredient');
                $inventoryService = app(InventoryService::class);
                foreach ($purchase->items as $item) {
                    $inventoryService->removeStockFromPurchaseItem($item);
                }
                $purchase->update(['is_stock_updated' => false]);
            });
        }
    }

    public function deleted(Purchase $purchase): void
    {
        if ($purchase->status === 'received' && $purchase->is_stock_updated) {
            $purchase->loadMissing('items.ingredient');
            $inventoryService = app(InventoryService::class);
            foreach ($purchase->items as $item) {
                $inventoryService->removeStockFromPurchaseItem($item);
            }
        }
    }
}
