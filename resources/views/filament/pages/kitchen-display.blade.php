<div>
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded">
            <div class="text-yellow-800 text-sm font-medium">Pending</div>
            <div class="text-3xl font-bold text-yellow-900">{{ $stats['pending'] ?? 0 }}</div>
        </div>
        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 rounded">
            <div class="text-blue-800 text-sm font-medium">Preparing</div>
            <div class="text-3xl font-bold text-blue-900">{{ $stats['preparing'] ?? 0 }}</div>
        </div>
        <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded">
            <div class="text-green-800 text-sm font-medium">Ready</div>
            <div class="text-3xl font-bold text-green-900">{{ $stats['ready'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-100 border-l-4 border-gray-500 p-4 rounded">
            <div class="text-gray-800 text-sm font-medium">Total</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <!-- Pending Column -->
        <div>
            <h2 class="text-lg font-bold text-yellow-700 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></span>
                Pending ({{ count($pendingKots) }})
            </h2>
            <div class="space-y-4">
                @forelse($pendingKots as $kot)
                    <div class="bg-white border-2 border-yellow-300 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-lg">{{ $kot['kot_number'] }}</div>
                                <div class="text-sm text-gray-600">
                                    Order: {{ $kot['order']['order_number'] ?? '-' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($kot['sent_at'])->diffForHumans() }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $kot['order']['order_type'] ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-3">
                            @foreach($kot['items'] as $item)
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                    <div>
                                        <span class="font-medium">{{ $item['item_name'] }}</span>
                                        <span class="text-sm text-gray-500">x{{ $item['quantity'] }}</span>
                                    </div>
                                    <button
                                        wire:click="updateItemStatus({{ $item['id'] }}, 'preparing')"
                                        class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600"
                                    >
                                        Start
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button
                            wire:click="updateKotStatus({{ $kot['id'] }}, 'preparing')"
                            class="w-full py-2 bg-blue-500 text-white rounded font-medium hover:bg-blue-600"
                        >
                            Start Preparing
                        </button>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">
                        No pending orders
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Preparing Column -->
        <div>
            <h2 class="text-lg font-bold text-blue-700 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
                Preparing ({{ count($preparingKots) }})
            </h2>
            <div class="space-y-4">
                @forelse($preparingKots as $kot)
                    <div class="bg-white border-2 border-blue-300 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-lg">{{ $kot['kot_number'] }}</div>
                                <div class="text-sm text-gray-600">
                                    Order: {{ $kot['order']['order_number'] ?? '-' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($kot['sent_at'])->diffForHumans() }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $kot['order']['order_type'] ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-3">
                            @foreach($kot['items'] as $item)
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                    <div class="flex items-center gap-2">
                                        @if($item['status'] === 'ready')
                                            <span class="text-green-500">✓</span>
                                        @endif
                                        <span class="font-medium">{{ $item['item_name'] }}</span>
                                        <span class="text-sm text-gray-500">x{{ $item['quantity'] }}</span>
                                    </div>
                                    @if($item['status'] !== 'ready')
                                        <button
                                            wire:click="updateItemStatus({{ $item['id'] }}, 'ready')"
                                            class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600"
                                        >
                                            Ready
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button
                            wire:click="updateKotStatus({{ $kot['id'] }}, 'ready')"
                            class="w-full py-2 bg-green-500 text-white rounded font-medium hover:bg-green-600"
                        >
                            Mark All Ready
                        </button>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">
                        No orders being prepared
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Ready Column -->
        <div>
            <h2 class="text-lg font-bold text-green-700 mb-4 flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                Ready ({{ count($readyKots) }})
            </h2>
            <div class="space-y-4">
                @forelse($readyKots as $kot)
                    <div class="bg-white border-2 border-green-300 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-lg">{{ $kot['kot_number'] }}</div>
                                <div class="text-sm text-gray-600">
                                    Order: {{ $kot['order']['order_number'] ?? '-' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-500">
                                    Ready {{ \Carbon\Carbon::parse($kot['ready_at'])->diffForHumans() }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $kot['order']['order_type'] ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-3">
                            @foreach($kot['items'] as $item)
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-500">✓</span>
                                        <span class="font-medium">{{ $item['item_name'] }}</span>
                                        <span class="text-sm text-gray-500">x{{ $item['quantity'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex gap-2">
                            <button
                                wire:click="printKot({{ $kot['id'] }})"
                                class="flex-1 py-2 bg-gray-600 text-white rounded font-medium hover:bg-gray-700"
                            >
                                Print
                            </button>
                            <button
                                wire:click="updateKotStatus({{ $kot['id'] }}, 'served')"
                                class="flex-1 py-2 bg-green-500 text-white rounded font-medium hover:bg-green-600"
                            >
                                Served
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-8">
                        No orders ready
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>