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

    protected ?OfflineSyncService $offlineSyncService = null;

    public function mount(?OfflineSyncService $offlineSyncService = null)
    {
        $this->offlineSyncService = $offlineSyncService ?? app(OfflineSyncService::class);
        $this->refreshStatus();
    }

    public function refreshStatus()
    {
        if ($this->offlineSyncService) {
            $this->pendingCount = $this->offlineSyncService->getPendingCount();
        } else {
            $this->pendingCount = 0;
        }
        $this->isOnline = $this->pendingCount === 0;
        $this->syncStatus = $this->isOnline ? 'synced' : 'offline';
    }

    public function manualSync()
    {
        if (! $this->offlineSyncService) {
            $this->dispatch('notify', type: 'error', message: 'Sync service not available');

            return;
        }

        try {
            $results = $this->offlineSyncService->processQueue();
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
