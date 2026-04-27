<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Services\InventoryService;

class PurchaseItemObserver
{
    public function created(PurchaseItem $item): void
    {
        if ($item->purchase->is_stock_updated) {
            app(InventoryService::class)->addStockFromPurchaseItem($item);
        }
    }

    public function updated(PurchaseItem $item): void
    {
        if ($item->purchase->is_stock_updated) {
            $inventoryService = app(InventoryService::class);

            // Revert old quantity
            $oldItem = clone $item;
            $oldItem->quantity = $item->getOriginal('quantity');
            $oldItem->ingredient_id = $item->getOriginal('ingredient_id');

            $inventoryService->removeStockFromPurchaseItem($oldItem);

            // Add new quantity
            $inventoryService->addStockFromPurchaseItem($item);
        }
    }

    public function deleted(PurchaseItem $item): void
    {
        if ($item->purchase->is_stock_updated) {
            app(InventoryService::class)->removeStockFromPurchaseItem($item);
        }
    }
}
