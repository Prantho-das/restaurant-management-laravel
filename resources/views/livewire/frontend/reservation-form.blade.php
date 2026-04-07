<form wire:submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 relative z-10 w-full">

    <div class="col-span-1 md:col-span-1 flex flex-col gap-2">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Your Name</label>
        <input wire:model="name" type="text" placeholder="Your Name Here" 
            class="border-b-2 border-brand-gold/20 py-3 lg:py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent rounded-none">
        @error('name') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>
    <div class="col-span-1 md:col-span-1 flex flex-col gap-2">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Contact Number</label>
        <input wire:model="phone" type="tel" placeholder="+880 17... " 
            class="border-b-2 border-brand-gold/20 py-3 lg:py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent rounded-none">
        @error('phone') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>

    <div class="col-span-1 md:col-span-1 flex flex-col gap-2">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Preferred Date</label>
        <input wire:model="date" type="date" 
            class="border-b-2 border-brand-gold/20 py-3 lg:py-4 text-sm font-bold focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent rounded-none">
        @error('date') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>
    <div class="col-span-1 md:col-span-1 flex flex-col gap-2">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Guest List Count</label>
        <select wire:model="guests" 
            class="border-b-2 border-brand-gold/20 py-3 lg:py-4 text-sm font-bold focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent rounded-none appearance-none cursor-pointer"
            style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23064e3b%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%205%205%205-5%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0 center; background-size: 16px;">
            <option value="02 PERS">02 PERS</option>
            <option value="04 PERS">04 PERS</option>
            <option value="06 PERS">06 PERS</option>
            <option value="08 PERS">08 PERS</option>
            <option value="ROYAL TABLE (10+)">ROYAL TABLE (10+)</option>
        </select>
        @error('guests') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>

    <div class="col-span-1 md:col-span-2 flex flex-col gap-2">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Special Arrangement Notes</label>
        <textarea wire:model="notes" placeholder="ANY ALLERGIES OR CELEBRATION DETAILS..." rows="3" 
            class="border-b-2 border-brand-gold/20 py-3 lg:py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent rounded-none resize-none"></textarea>
    </div>

    <div class="col-span-1 md:col-span-2 pt-6 lg:pt-8">
        <button type="submit" 
            class="btn-emerald w-full py-4 lg:py-5 text-xs lg:text-sm tracking-[0.3em] uppercase shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all @if($isSuccess) opacity-50 cursor-not-allowed @endif"
            @if($isSuccess) disabled @endif>
            <div class="inline-grid grid-cols-1 grid-rows-1 items-center justify-center">
                <div wire:loading.remove wire:target="submit" class="col-start-1 row-start-1 flex items-center justify-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span>{{ $isSuccess ? 'Table Requested' : 'Secure the Table' }}</span>
                </div>
                <div wire:loading wire:target="submit" class="col-start-1 row-start-1 flex items-center justify-center gap-3">
                    <svg class="animate-spin w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span>Processing...</span>
                </div>
            </div>
        </button>
        <p class="text-center text-[9px] uppercase font-bold tracking-[0.2em] text-brand-emerald/30 mt-6 lg:mt-8">Subject to availability. Our concierge will confirm via call.</p>
    </div>
</form>