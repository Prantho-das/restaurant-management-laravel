<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\StockAdjustment;
use App\Models\Wastage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Deduct ingredient stock based on the order items and their recipes.
     */
    public function deductStockForOrder(Order $order): void
    {
        if ($order->is_stock_deducted) {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $menuItem = $item->menuItem;

                if (! $menuItem) {
                    continue;
                }

                // Only deduct stock if the item is NOT pre-made (cooked on demand)
                if ($menuItem->preparation_type === 'premade') {
                    continue;
                }

                // Load recipes for the menu item
                foreach ($menuItem->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if (! $ingredient) {
                        continue;
                    }
                    $deductionQuantity = $recipe->quantity * $item->quantity;

                    // Deduct stock
                    $ingredient->decrement('current_stock', $deductionQuantity);

                    // Log the deduction
                    InventoryLog::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'deduction',
                        'quantity' => $deductionQuantity,
                        'note' => "Order #{$order->order_number} (Made-to-order)",
                        'user_id' => $order->user_id ?? Auth::id(),
                    ]);
                }
            }

            $order->update(['is_stock_deducted' => true]);
        });
    }

    /**
     * Restore ingredient stock based on the order items and their recipes.
     */
    public function restoreStockForOrder(Order $order): void
    {
        if (! $order->is_stock_deducted) {
            return;
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $menuItem = $item->menuItem;

                if (! $menuItem) {
                    continue;
                }

                if ($menuItem->preparation_type === 'premade') {
                    continue;
                }

                foreach ($menuItem->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if (! $ingredient) {
                        continue;
                    }
                    $restoredQuantity = $recipe->quantity * $item->quantity;

                    $ingredient->increment('current_stock', $restoredQuantity);

                    InventoryLog::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'restock',
                        'quantity' => $restoredQuantity,
                        'note' => "Stock restored for Order #{$order->order_number}",
                        'user_id' => Auth::id() ?? $order->user_id,
                    ]);
                }
            }

            $order->update(['is_stock_deducted' => false]);
        });
    }

    /**
     * Add stock for an ingredient (Restock).
     */
    public function restock(int $ingredientId, float $quantity, ?string $note = null): void
    {
        DB::transaction(function () use ($ingredientId, $quantity, $note) {
            $ingredient = Ingredient::findOrFail($ingredientId);
            $ingredient->increment('current_stock', $quantity);

            InventoryLog::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'restock',
                'quantity' => $quantity,
                'note' => $note,
                'user_id' => Auth::id(),
            ]);
        });
    }

    /**
     * Add stock from a purchase and record in logs.
     */
    public function addStockFromPurchase(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                $item->ingredient->increment('current_stock', $item->quantity);

                InventoryLog::create([
                    'ingredient_id' => $item->ingredient_id,
                    'type' => 'stock_in',
                    'quantity' => $item->quantity,
                    'note' => "Purchase #{$purchase->reference_no}",
                    'user_id' => $purchase->user_id,
                ]);
            }
        });
    }

    /**
     * Process a stock adjustment and update inventory levels.
     */
    public function processStockAdjustment(StockAdjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->items as $item) {
                if ($item->type === 'addition') {
                    $item->ingredient->increment('current_stock', $item->quantity);
                } else {
                    $item->ingredient->decrement('current_stock', $item->quantity);
                }

                InventoryLog::create([
                    'ingredient_id' => $item->ingredient_id,
                    'type' => 'stock_adjustment',
                    'quantity' => $item->quantity,
                    'note' => $item->note ?? "Stock Adjustment #{$adjustment->reference_no}",
                    'user_id' => $adjustment->user_id,
                ]);
            }
        });
    }

    /**
     * Process stock deduction for recorded wastage.
     */
    public function deductStockForWastage(Wastage $wastage): void
    {
        DB::transaction(function () use ($wastage) {
            if ($wastage->ingredient_id) {
                $ingredient = $wastage->ingredient;
                if ($ingredient) {
                    $ingredient->decrement('current_stock', $wastage->quantity);

                    InventoryLog::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'wastage',
                        'quantity' => $wastage->quantity,
                        'note' => 'Wastage: '.ucfirst($wastage->reason).($wastage->notes ? " - {$wastage->notes}" : ''),
                        'user_id' => $wastage->user_id,
                    ]);
                }
            } elseif ($wastage->menu_item_id) {
                $menuItem = $wastage->menuItem;
                if ($menuItem) {
                    foreach ($menuItem->recipes as $recipe) {
                        $ingredient = $recipe->ingredient;
                        if ($ingredient) {
                            $deductionQuantity = $recipe->quantity * $wastage->quantity;

                            $ingredient->decrement('current_stock', $deductionQuantity);

                            InventoryLog::create([
                                'ingredient_id' => $ingredient->id,
                                'type' => 'wastage',
                                'quantity' => $deductionQuantity,
                                'note' => "Wastage of {$menuItem->name} (Reason: ".ucfirst($wastage->reason).')',
                                'user_id' => $wastage->user_id,
                            ]);
                        }
                    }
                }
            }
        });
    }
}
