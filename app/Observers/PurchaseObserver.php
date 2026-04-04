<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Purchase;
use App\Services\InventoryService;

class PurchaseObserver
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        if ($purchase->isDirty('status') && $purchase->status === 'received' && $purchase->getOriginal('status') !== 'received') {
            // 1. Update stock and log inventory
            $this->inventoryService->addStockFromPurchase($purchase);

            // 2. Create an expense record
            Expense::create([
                'category' => 'Purchase',
                'title' => 'Purchase from '.($purchase->supplier?->name ?? 'Supplier'),
                'description' => "Reference: #{$purchase->reference_no}",
                'amount' => $purchase->total_amount,
                'date' => $purchase->purchase_date ?? now(),
                'payment_method' => 'Cash',
                'reference_no' => $purchase->reference_no,
                'user_id' => $purchase->user_id,
            ]);
        }
    }
}
