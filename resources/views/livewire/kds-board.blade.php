<div wire:poll.5s="refreshData" class="h-full flex gap-6 overflow-x-auto pb-4">
    
    <!-- Audio element for notifications -->
    <audio id="kds-alert" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
    
    <!-- Notification logic -->
    <script>
        document.addEventListener('livewire:initialized', () => {
             let previousPendingCount = {{ count($pendingKots) }};
             
             Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                 succeed(({ snapshot, effect }) => {
                     // Check if pending orders increased
                     let currentPendingCount = Object.keys(snapshot.data.pendingKots || {}).length;
                     if(currentPendingCount > previousPendingCount) {
                         // Play sound
                         const audio = document.getElementById('kds-alert');
                         if(audio) {
                             audio.play().catch(e => console.log('Audio autoplay prevented'));
                         }
                     }
                     previousPendingCount = currentPendingCount;
                 })
             })
        });
    </script>

    <!-- Pending Lane -->
    <div class="flex-none w-[400px] h-full flex flex-col pt-1">
        <div class="bg-gray-800 border-t-4 border-yellow-500 rounded-lg p-4 mb-4 shadow-lg flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-100 flex items-center gap-2">
                <span class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(234,179,8,0.8)]"></span>
                NEW ORDERS
            </h2>
            <span class="bg-yellow-500 text-gray-900 font-bold px-3 py-1 rounded-full text-lg">{{ count($pendingKots) }}</span>
        </div>
        
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
            @forelse($pendingKots as $kot)
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-xl transform transition hover:-translate-y-1">
                    <div class="bg-yellow-500/10 border-b border-gray-700 p-3 flex justify-between items-center">
                        <div>
                            <div class="font-black text-xl text-yellow-400">#{{ $kot['kot_number'] }}</div>
                            <div class="text-sm text-gray-400 font-medium">Order: {{ $kot['order']['order_number'] ?? 'N/A' }}</div>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <div class="bg-gray-700 text-gray-300 text-xs px-2 py-1 rounded mb-1 font-bold">{{ strtoupper($kot['order']['order_type'] ?? 'DINE-IN') }}</div>
                            <div class="text-sm text-gray-400 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ \Carbon\Carbon::parse($kot['sent_at'])->diffForHumans(null, true, true) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        @foreach($kot['items'] as $item)
                        <div class="flex justify-between items-start border-b border-gray-700/50 pb-2 last:border-0 last:pb-0">
                            <div>
                                <div class="text-lg font-bold text-gray-100">{{ $item['item_name'] }}</div>
                                @if($item['notes'])
                                    <div class="text-sm text-red-400 mt-1 italic whitespace-pre-wrap">Note: {{ $item['notes'] }}</div>
                                @endif
                            </div>
                            <div class="text-xl font-black text-white bg-gray-700 px-3 py-1 rounded">x{{ $item['quantity'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="p-3 bg-gray-800 border-t border-gray-700">
                        <button wire:click="updateKotStatus({{ $kot['id'] }}, 'preparing')" class="w-full py-4 bg-yellow-600 hover:bg-yellow-500 text-white font-bold text-lg rounded-lg shadow-lg transition transform active:scale-95">
                            START PREPARING
                        </button>
                    </div>
                </div>
            @empty
                <div class="h-32 flex items-center justify-center border-2 border-dashed border-gray-700 rounded-xl text-gray-500 font-medium">
                    No new orders
                </div>
            @endforelse
        </div>
    </div>

    <!-- Preparing Lane -->
    <div class="flex-none w-[400px] h-full flex flex-col pt-1">
        <div class="bg-gray-800 border-t-4 border-blue-500 rounded-lg p-4 mb-4 shadow-lg flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-100 flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.8)]"></span>
                PREPARING
            </h2>
            <span class="bg-blue-600 text-white font-bold px-3 py-1 rounded-full text-lg">{{ count($preparingKots) }}</span>
        </div>
        
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
            @forelse($preparingKots as $kot)
                <div class="bg-gray-800 border-2 border-blue-600/30 rounded-xl overflow-hidden shadow-xl">
                    <div class="bg-blue-900/40 border-b border-gray-700 p-3 flex justify-between items-center">
                        <div>
                            <div class="font-black text-xl text-blue-400">#{{ $kot['kot_number'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-400 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ \Carbon\Carbon::parse($kot['preparing_at'])->diffForHumans(null, true, true) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-3">
                        @foreach($kot['items'] as $item)
                        <div class="flex justify-between items-center border-b border-gray-700/50 pb-2 last:border-0 last:pb-0">
                            <div class="flex-1">
                                <div class="text-lg font-bold {{ $item['status'] === 'ready' ? 'text-green-400 line-through opacity-70' : 'text-gray-100' }}">
                                    {{ $item['item_name'] }}
                                </div>
                                <div class="text-sm text-gray-400">Qty: {{ $item['quantity'] }}</div>
                            </div>
                            <div>
                                @if($item['status'] !== 'ready')
                                    <button wire:click="updateItemStatus({{ $item['id'] }}, 'ready')" class="px-4 py-2 bg-gray-700 hover:bg-green-600 text-white font-bold rounded shadow transition active:scale-95">
                                        DONE
                                    </button>
                                @else
                                    <span class="bg-green-900/50 text-green-400 px-3 py-1 rounded font-bold border border-green-800">
                                        ✓ READY
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="p-3 bg-gray-800 border-t border-gray-700">
                        <button wire:click="updateKotStatus({{ $kot['id'] }}, 'ready')" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold text-lg rounded-lg shadow-lg transition transform active:scale-95">
                            MARK ORDER READY
                        </button>
                    </div>
                </div>
            @empty
                <div class="h-32 flex items-center justify-center border-2 border-dashed border-gray-700 rounded-xl text-gray-500 font-medium">
                    No orders being prepared
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ready Lane -->
    <div class="flex-none w-[400px] h-full flex flex-col pt-1">
        <div class="bg-gray-800 border-t-4 border-green-500 rounded-lg p-4 mb-4 shadow-lg flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-100 flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_10px_rgba(34,197,94,0.8)]"></span>
                READY TO SERVE
            </h2>
            <span class="bg-green-600 text-white font-bold px-3 py-1 rounded-full text-lg">{{ count($readyKots) }}</span>
        </div>
        
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
            @forelse($readyKots as $kot)
                <div class="bg-gray-800 border-2 border-green-600/50 rounded-xl overflow-hidden shadow-xl opacity-90 hover:opacity-100 transition">
                    <div class="bg-green-900/30 border-b border-gray-700 p-3 flex justify-between items-center">
                        <div class="font-black text-xl text-green-400">#{{ $kot['kot_number'] }}</div>
                        <div class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($kot['ready_at'])->diffForHumans(null, true, true) }}</div>
                    </div>
                    
                    <div class="p-4">
                        <div class="text-gray-300 font-medium mb-3">
                            Order {{ $kot['order']['order_number'] ?? 'N/A' }} 
                            ({{ $kot['order']['order_type'] ?? 'DINE-IN' }})
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($kot['items'] as $item)
                                <span class="bg-gray-700 text-gray-200 text-xs px-2 py-1 rounded-md">
                                    {{ $item['quantity'] }}x {{ $item['item_name'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="p-3 bg-gray-800 border-t border-gray-700 flex gap-2">
                        <button wire:click="updateKotStatus({{ $kot['id'] }}, 'served')" class="flex-1 py-3 bg-green-600 hover:bg-green-500 text-white font-bold text-lg rounded-lg shadow-lg transition active:scale-95">
                            DISPATCH
                        </button>
                    </div>
                </div>
            @empty
                <div class="h-32 flex items-center justify-center border-2 border-dashed border-gray-700 rounded-xl text-gray-500 font-medium">
                    No orders waiting
                </div>
            @endforelse
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937; 
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563; 
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280; 
        }
    </style>
</div>
