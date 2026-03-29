<?php

namespace App\Services;

use App\Models\OfflineSyncQueue;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OfflineSyncService
{
    /**
     * Queue an order for later synchronization.
     */
    public function queueOrder(array $orderData, array $items): OfflineSyncQueue
    {
        $syncToken = Str::uuid()->toString();

        return DB::transaction(function () use ($orderData, $items, $syncToken) {
            $queueItem = OfflineSyncQueue::create([
                'order_number' => $orderData['order_number'],
                'status' => 'pending',
                'subtotal_amount' => $orderData['subtotal_amount'],
                'discount_amount' => $orderData['discount_amount'] ?? 0,
                'discount_type' => $orderData['discount_type'] ?? 'fixed',
                'total_amount' => $orderData['total_amount'],
                'order_type' => $orderData['order_type'],
                'payment_method' => $orderData['payment_method'],
                'table_number' => $orderData['table_number'] ?? null,
                'customer_name' => $orderData['customer_name'] ?? null,
                'customer_phone' => $orderData['customer_phone'] ?? null,
                'guest_count' => $orderData['guest_count'] ?? 1,
                'notes' => $orderData['notes'] ?? null,
                'reference_no' => $orderData['reference_no'] ?? null,
                'user_id' => $orderData['user_id'] ?? auth()->id(),
                'items' => json_encode($items),
                'sync_token' => $syncToken,
            ]);

            return $queueItem;
        });
    }

    /**
     * Process the sync queue and attempt to sync all pending orders.
     */
    public function processQueue(): array
    {
        $results = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $pendingItems = OfflineSyncQueue::where('sync_attempts', '<', 3)
            ->orderBy('created_at')
            ->get();

        $results['total'] = $pendingItems->count();

        foreach ($pendingItems as $queueItem) {
            try {
                DB::transaction(function () use ($queueItem) {
                    // Create the order
                    $order = Order::create([
                        'order_number' => $queueItem->order_number,
                        'status' => 'pending',
                        'subtotal_amount' => $queueItem->subtotal_amount,
                        'discount_amount' => $queueItem->discount_amount,
                        'discount_type' => $queueItem->discount_type,
                        'total_amount' => $queueItem->total_amount,
                        'order_type' => $queueItem->order_type,
                        'payment_method' => $queueItem->payment_method,
                        'table_number' => $queueItem->table_number,
                        'customer_name' => $queueItem->customer_name,
                        'customer_phone' => $queueItem->customer_phone,
                        'guest_count' => $queueItem->guest_count,
                        'notes' => $queueItem->notes.($queueItem->notes ? "\n" : '').'(Synced from offline)',
                        'reference_no' => $queueItem->reference_no,
                        'user_id' => $queueItem->user_id,
                        'is_offline' => true,
                        'synced_at' => now(),
                    ]);

                    // Create order items
                    $items = json_decode($queueItem->items, true);
                    foreach ($items as $item) {
                        $order->items()->create([
                            'menu_item_id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ]);
                    }

                    // Mark queue item as synced
                    $queueItem->update([
                        'synced_at' => now(),
                        'sync_token' => null,
                        'sync_attempts' => $queueItem->sync_attempts + 1,
                    ]);
                });

                $results['success']++;
            } catch (Throwable $e) {
                $queueItem->increment('sync_attempts');
                $queueItem->update([
                    'sync_error' => $e->getMessage(),
                ]);

                $results['failed']++;
                $results['errors'][] = [
                    'order' => $queueItem->order_number,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get pending offline orders count.
     */
    public function getPendingCount(): int
    {
        return OfflineSyncQueue::where('synced_at', null)->count();
    }

    /**
     * Get all pending offline orders.
     */
    public function getPendingOrders()
    {
        return OfflineSyncQueue::where('synced_at', null)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if system is offline (has pending sync items).
     */
    public function isOffline(): bool
    {
        return $this->getPendingCount() > 0;
    }

    /**
     * Clear successfully synced items from queue (older than X days).
     */
    public function cleanSyncedItems(int $days = 30): int
    {
        return OfflineSyncQueue::whereNotNull('synced_at')
            ->where('synced_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Retry failed sync items (reset sync_attempts).
     */
    public function retryFailedItems(): int
    {
        return OfflineSyncQueue::where('sync_attempts', '>=', 3)
            ->update([
                'sync_attempts' => 0,
                'sync_error' => null,
            ]);
    }
}
