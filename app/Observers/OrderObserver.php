<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\InventoryService;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            if ($order->status === 'completed') {
                app(InventoryService::class)->deductStockForOrder($order->loadMissing('items.menuItem.recipes.ingredient'));
            } else {
                // If it changes FROM completed to anything else, restore stock
                app(InventoryService::class)->restoreStockForOrder($order->loadMissing('items.menuItem.recipes.ingredient'));
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        app(InventoryService::class)->restoreStockForOrder($order->loadMissing('items.menuItem.recipes.ingredient'));
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        app(InventoryService::class)->restoreStockForOrder($order->loadMissing('items.menuItem.recipes.ingredient'));
    }
}
