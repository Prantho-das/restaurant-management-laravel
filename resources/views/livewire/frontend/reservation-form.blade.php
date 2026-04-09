<form wire:submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10 w-full">

    <div class="flex flex-col gap-2">
        <label class="text-xs font-bold uppercase tracking-wider text-[#666666]">আপনার নাম</label>
        <input wire:model="name" type="text" placeholder="আপনার নাম" 
            class="border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-[#c01c1c] transition-colors text-[#333333] bg-white">
        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div class="flex flex-col gap-2">
        <label class="text-xs font-bold uppercase tracking-wider text-[#666666]">ফোন নম্বর</label>
        <input wire:model="phone" type="tel" placeholder="+880 17..." 
            class="border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-[#c01c1c] transition-colors text-[#333333] bg-white">
        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="flex flex-col gap-2">
        <label class="text-xs font-bold uppercase tracking-wider text-[#666666]">তারিখ</label>
        <input wire:model="date" type="date" 
            class="border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-[#c01c1c] transition-colors text-[#333333] bg-white">
        @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div class="flex flex-col gap-2">
        <label class="text-xs font-bold uppercase tracking-wider text-[#666666]">গেস্ট সংখ্যা</label>
        <select wire:model="guests" 
            class="border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-[#c01c1c] transition-colors text-[#333333] bg-white">
            <option value="02">২ জন</option>
            <option value="04">৪ জন</option>
            <option value="06">৬ জন</option>
            <option value="08">৮ জন</option>
            <option value="10+">১০+ জন</option>
        </select>
        @error('guests') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="md:col-span-2 flex flex-col gap-2">
        <label class="text-xs font-bold uppercase tracking-wider text-[#666666]">বিশেষ নোট</label>
        <textarea wire:model="notes" placeholder="কোনো বিশেষ অনুরোধ..." rows="3" 
            class="border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-[#c01c1c] transition-colors text-[#333333] bg-white resize-none"></textarea>
    </div>

    <div class="md:col-span-2 pt-4">
        <button type="submit" 
            class="w-full py-3 text-sm font-bold uppercase tracking-wider rounded-lg bg-[#c01c1c] text-white hover:bg-[#d92e2e] transition-all @if($isSuccess) opacity-50 cursor-not-allowed @endif"
            @if($isSuccess) disabled @endif>
            @if($isSuccess)
                <span>বুকিং সফল</span>
            @else
                <span>বুক করুন</span>
            @endif
        </button>
        <p class="text-center text-xs text-[#666666] mt-4">আমাদের টিম আপনাকে যোগাযোগ করবে।</p>
    </div>
</form>