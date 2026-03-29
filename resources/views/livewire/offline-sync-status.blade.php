<div class="fixed top-4 right-4 z-50">
    <div
        wire:poll.5s="refreshStatus"
        x-data="{
            browserOnline: navigator.onLine,
            pendingCount: @entangle('pendingCount')
        }"
        x-init="window.addEventListener('online', () => browserOnline = true); window.addEventListener('offline', () => browserOnline = false);"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-2 p-4 transition-all duration-300"
        :class="{
            'border-green-500': browserOnline && pendingCount == 0,
            'border-red-500': !browserOnline,
            'border-yellow-500': browserOnline && pendingCount > 0
        }"
    >
        <div class="flex items-center space-x-3">
            <!-- Status Indicator -->
            <div class="relative">
                <div
                    class="w-3 h-3 rounded-full animate-pulse"
                    :class="{
                        'bg-green-500': browserOnline && pendingCount == 0,
                        'bg-red-500': !browserOnline,
                        'bg-yellow-500': browserOnline && pendingCount > 0
                    }"
                ></div>
                @if($pendingCount > 0)
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-600 rounded-full flex items-center justify-center text-xs text-white font-bold" x-text="pendingCount">
                        {{ $pendingCount }}
                    </div>
                @endif
            </div>

            <!-- Status Text -->
            <div class="flex flex-col">
                <span class="text-sm font-semibold" :class="{
                    'text-green-600 dark:text-green-400': browserOnline && pendingCount == 0,
                    'text-red-600 dark:text-red-400': !browserOnline,
                    'text-yellow-600 dark:text-yellow-400': browserOnline && pendingCount > 0
                }">
                    <span x-text="browserOnline ? 'Online' : 'Offline'"></span>
                    <span x-show="browserOnline && pendingCount > 0"> (Syncing)</span>
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="pendingCount > 0 ? pendingCount + ' pending on server' : 'All synced'">
                    {{ $pendingCount > 0 ? $pendingCount . ' pending on server' : 'All synced' }}
                </span>
            </div>

            <!-- Sync Button (shown when online and there are pending server items) -->
            <button
                x-show="browserOnline && pendingCount > 0"
                wire:click="manualSync"
                wire:loading.attr="disabled"
                class="ml-2 px-3 py-1 text-xs bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading
            >
                <span wire:loading.remove>Sync Now</span>
                <span wire:loading>Syncing...</span>
            </button>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</div>
