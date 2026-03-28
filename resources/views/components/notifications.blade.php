<div x-data="{ 
    notifications: [],
    init() {
        window.addEventListener('notify', e => {
            const id = Date.now();
            const data = e.detail[0] || e.detail;
            this.notifications.push({ id, ...data });
            setTimeout(() => {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }, 5000);
        });
    }
}" 
class="fixed top-8 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-4 pointer-events-none w-full max-w-sm font-sans">
    <template x-for="n in notifications" :key="n.id">
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
             class="px-6 py-4 rounded-3xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] flex items-center gap-5 pointer-events-auto border border-white/10 backdrop-blur-xl"
            :class="{ 
                'bg-emerald-600/95 text-white shadow-emerald-900/40': n.type === 'success',
                'bg-rose-600/95 text-white shadow-rose-900/40': n.type === 'error',
                'bg-amber-500/95 text-white shadow-amber-900/40': n.type === 'warning'
            }">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 bg-white/20">
                <template x-if="n.type === 'success'">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                </template>
                <template x-if="n.type === 'error'">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </template>
                <template x-if="n.type === 'warning'">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                </template>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[12px] font-black leading-tight uppercase tracking-wider" x-text="n.message"></p>
            </div>
            <button @click="notifications = notifications.filter(x => x.id !== n.id)" class="w-7 h-7 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>
</div>
