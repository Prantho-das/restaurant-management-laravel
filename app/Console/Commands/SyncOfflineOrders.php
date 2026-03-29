<?php

namespace App\Console\Commands;

use App\Services\OfflineSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:sync-offline-orders')]
#[Description('Process the offline sync queue and sync all pending orders to the main database.')]
class SyncOfflineOrders extends Command
{
    public function __construct(
        protected OfflineSyncService $offlineSyncService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Processing offline sync queue...');

        $results = $this->offlineSyncService->processQueue();

        $this->info('Sync completed!');
        $this->table(
            ['Total', 'Success', 'Failed'],
            [[$results['total'], $results['success'], $results['failed']]]
        );

        if (! empty($results['errors'])) {
            $this->warning('Errors:');
            foreach ($results['errors'] as $error) {
                $this->warn("Order {$error['order']}: {$error['error']}");
            }
        }

        return self::SUCCESS;
    }
}
