<form wire:submit.prevent="submit" class="grid grid-cols-2 gap-10 relative z-10 w-full">
    @if($isSuccess)
        <div class="col-span-2 bg-brand-emerald/10 border border-brand-emerald text-brand-emerald p-6 rounded-xl flex items-center justify-center gap-4 animate-fade-in-up">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h4 class="font-bold uppercase tracking-widest text-sm mb-1">Reservation Requested</h4>
                <p class="text-xs">Your table request has been received. Our concierge will contact you shortly.</p>
            </div>
        </div>
    @endif

    <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Your Name</label>
        <input wire:model="name" type="text" placeholder="SHAHID KHAN" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent">
        @error('name') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>
    <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Contact Number</label>
        <input wire:model="phone" type="tel" placeholder="+880 17... " class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent">
        @error('phone') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>

    <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Preferred Date</label>
        <input wire:model="date" type="date" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent">
        @error('date') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>
    <div class="col-span-2 md:col-span-1 flex flex-col gap-4">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Guest List Count</label>
        <select wire:model="guests" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent">
            <option value="02 PERS">02 PERS</option>
            <option value="04 PERS">04 PERS</option>
            <option value="06 PERS">06 PERS</option>
            <option value="ROYAL TABLE (10+)">ROYAL TABLE (10+)</option>
        </select>
        @error('guests') <span class="text-brand-red text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
    </div>

    <div class="col-span-2 flex flex-col gap-4">
        <label class="text-[10px] uppercase font-bold tracking-[0.3em] text-brand-gold">Special Arrangement Notes</label>
        <textarea wire:model="notes" placeholder="ANY ALLERGIES OR CELEBRATION DETAILS..." rows="2" class="border-b-2 border-brand-gold/20 py-4 text-sm font-bold placeholder:text-brand-emerald/20 focus:outline-none focus:border-brand-emerald transition-colors text-brand-emerald bg-transparent"></textarea>
    </div>

    <div class="col-span-2 pt-10">
        <button type="submit" class="btn-royal w-full py-6 text-xs tracking-[0.5em] uppercase shadow-xl hover:shadow-brand-emerald/20" @if($isSuccess) disabled @endif>
             <span wire:loading.remove wire:target="submit">
                 {{ $isSuccess ? 'Table Requested' : 'Secure the Table' }}
             </span>
             <span wire:loading wire:target="submit">Processing...</span>
        </button>
        <p class="text-center text-[9px] uppercase font-bold tracking-[0.2em] text-brand-emerald/30 mt-8">Subject to availability. Our concierge will confirm via call.</p>
    </div>
</form>
