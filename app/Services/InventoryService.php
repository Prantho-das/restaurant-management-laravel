<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\InventoryLog;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Deduct ingredient stock based on the order items and their recipes.
     */
    public function deductStockForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $menuItem = $item->menuItem;

                // Load recipes for the menu item
                foreach ($menuItem->recipes as $recipe) {
                    $ingredient = $recipe->ingredient;
                    $deductionQuantity = $recipe->quantity * $item->quantity;

                    // Deduct stock
                    $ingredient->decrement('current_stock', $deductionQuantity);

                    // Log the deduction
                    InventoryLog::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'deduction',
                        'quantity' => $deductionQuantity,
                        'note' => "Order #{$order->order_number}",
                        // 'user_id' => auth()->id(), // Set if applicable
                    ]);
                }
            }
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
                'user_id' => auth()->id(),
            ]);
        });
    }
}
