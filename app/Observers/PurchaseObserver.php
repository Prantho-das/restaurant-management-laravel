<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Purchase;

class PurchaseObserver
{
    public function created(Purchase $purchase): void
    {
    }
}