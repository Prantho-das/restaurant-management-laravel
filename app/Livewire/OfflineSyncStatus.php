<?php

namespace App\Livewire;

use App\Services\OfflineSyncService;
use Livewire\Component;

class OfflineSyncStatus extends Component
{
    public bool $isOnline = true;

    public int $pendingCount = 0;

    public string $syncStatus = 'synced';

    public string $lastSyncError = '';

    public function refreshStatus()
    {
        try {
            $offlineSyncService = app(OfflineSyncService::class);
            $this->pendingCount = $offlineSyncService->getPendingCount();
        } catch (\Throwable $e) {
            $this->pendingCount = 0;
            $this->lastSyncError = $e->getMessage();
        }

        $this->isOnline = $this->pendingCount === 0;
        $this->syncStatus = $this->isOnline ? 'synced' : 'offline';
    }

    public function manualSync()
    {
        try {
            $offlineSyncService = app(OfflineSyncService::class);
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: 'Sync service not available');

            return;
        }

        try {
            $results = $offlineSyncService->processQueue();
            $this->refreshStatus();

            if ($results['success'] > 0) {
                $this->dispatch('notify', type: 'success', message: "{$results['success']} order(s) synced successfully!");
            }

            if ($results['failed'] > 0) {
                $this->dispatch('notify', type: 'error', message: "{$results['failed']} order(s) failed to sync. Check errors.");
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Sync failed: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.offline-sync-status');
    }
}
