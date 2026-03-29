<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OfflineSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflineSyncController extends Controller
{
    public function __construct(
        protected OfflineSyncService $offlineSyncService
    ) {}

    /**
     * Queue an order for offline synchronization.
     */
    public function queueOrder(Request $request): JsonResponse
    {
        $request->validate([
            'order_number' => 'required|string|unique:offline_sync_queue,order_number',
            'subtotal_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'order_type' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:menu_items,id',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $queueItem = $this->offlineSyncService->queueOrder(
                $request->only([
                    'order_number',
                    'subtotal_amount',
                    'discount_amount',
                    'discount_type',
                    'total_amount',
                    'order_type',
                    'payment_method',
                    'table_number',
                    'customer_name',
                    'customer_phone',
                    'guest_count',
                    'notes',
                    'reference_no',
                ]),
                $request->input('items', [])
            );

            return response()->json([
                'success' => true,
                'message' => 'Order queued for synchronization',
                'data' => [
                    'sync_token' => $queueItem->sync_token,
                    'order_number' => $queueItem->order_number,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get pending offline orders count.
     */
    public function pendingCount(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => $this->offlineSyncService->getPendingCount(),
        ]);
    }

    /**
     * Process the sync queue (admin/scheduled endpoint).
     */
    public function processQueue(): JsonResponse
    {
        try {
            $results = $this->offlineSyncService->processQueue();

            return response()->json([
                'success' => true,
                'message' => 'Sync completed',
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if system is offline.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'is_offline' => $this->offlineSyncService->isOffline(),
            'pending_count' => $this->offlineSyncService->getPendingCount(),
        ]);
    }
}
