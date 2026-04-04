<?php

namespace App\Observers;

use App\Models\Wastage;
use App\Services\InventoryService;

class WastageObserver
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Handle the Wastage "created" event.
     */
    public function created(Wastage $wastage): void
    {
        $this->inventoryService->deductStockForWastage($wastage);
    }
}
