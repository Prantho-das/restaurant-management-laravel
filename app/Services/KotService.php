<?php

namespace App\Services;

use App\Models\KotOrder;
use App\Models\KotOrderItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class KotService
{
    /**
     * Send an order to kitchen (create KOT).
     */
    public function sendToKitchen(Order $order, int $userId): KotOrder
    {
        return DB::transaction(function () use ($order, $userId) {
            // Create KOT order
            $kotOrder = KotOrder::create([
                'order_id' => $order->id,
                'kot_number' => KotOrder::generateKotNumber(),
                'status' => 'pending',
                'sent_at' => now(),
                'sent_by' => $userId,
            ]);

            // Create KOT items from order items
            foreach ($order->items as $orderItem) {
                KotOrderItem::create([
                    'kot_order_id' => $kotOrder->id,
                    'order_item_id' => $orderItem->id,
                    'menu_item_id' => $orderItem->menu_item_id,
                    'item_name' => $orderItem->menuItem?->name ?? 'Unknown Item',
                    'quantity' => $orderItem->quantity,
                    'notes' => $orderItem->notes ?? $orderItem->menuItem?->description,
                    'status' => 'pending',
                ]);
            }

            return $kotOrder;
        });
    }

    /**
     * Update KOT status (preparing, ready, served, cancelled).
     */
    public function updateKotStatus(KotOrder $kotOrder, string $status, int $userId, ?string $reason = null): bool
    {
        return match ($status) {
            'preparing' => $kotOrder->startPreparing($userId),
            'ready' => $kotOrder->markAsReady($userId),
            'served' => $kotOrder->markAsServed($userId),
            'cancelled' => $kotOrder->cancel($userId, $reason),
            default => false,
        };
    }

    /**
     * Update item status in KOT.
     */
    public function updateItemStatus(KotOrderItem $item, string $status, ?string $reason = null): bool
    {
        return match ($status) {
            'preparing' => $item->startPreparing(),
            'ready' => $item->markAsReady(),
            'served' => $item->markAsServed(),
            'cancelled' => $item->cancel($reason),
            default => false,
        };
    }

    /**
     * Get pending KOTs for KDS display.
     */
    public function getPendingKots()
    {
        return KotOrder::with(['order', 'items', 'sentBy'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('sent_at', 'asc')
            ->get();
    }

    /**
     * Get KOT statistics for dashboard.
     */
    public function getKotStats(): array
    {
        return [
            'pending' => KotOrder::where('status', 'pending')->count(),
            'preparing' => KotOrder::where('status', 'preparing')->count(),
            'ready' => KotOrder::where('status', 'ready')->count(),
            'total' => KotOrder::whereIn('status', ['pending', 'preparing', 'ready'])->count(),
        ];
    }
}
