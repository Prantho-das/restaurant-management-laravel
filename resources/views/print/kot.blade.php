<div class="text-center border-b-2 border-dashed border-gray-300 pb-2 mb-2">
    <div class="text-lg font-bold uppercase">{{ $restaurantName ?? 'Restaurant' }}</div>
    <div class="text-xs">{{ $restaurantAddress ?? '' }}</div>
    <div class="text-xs">Tel: {{ $restaurantPhone ?? '' }}</div>
</div>

<div class="mb-2">
    <div class="text-center font-bold text-xl">{{ $kotNumber }}</div>
    <div class="text-center text-sm">Order: {{ $orderNumber }}</div>
</div>

<div class="flex justify-between text-sm mb-2">
    <div>Date: {{ $date }}</div>
    <div>Time: {{ $time }}</div>
</div>

<div class="flex justify-between text-sm mb-2">
    <div>Type: {{ $orderType }}</div>
    @if($tableNumber)
    <div>Table: {{ $tableNumber }}</div>
    @endif
</div>

<div class="border-t border-b border-gray-300 py-2 mb-2">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b">
                <th class="text-left">Item</th>
                <th class="text-right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="py-1">
                    {{ $item['item_name'] }}
                    @if($item['notes'])
                    <div class="text-xs text-gray-500 italic">{{ $item['notes'] }}</div>
                    @endif
                </td>
                <td class="text-right py-1">{{ $item['quantity'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($notes)
<div class="text-sm">
    <strong>Notes:</strong> {{ $notes }}
</div>
@endif

<div class="text-center text-xs mt-4 border-t pt-2">
    Sent by: {{ $sentBy }}
</div>

<div class="text-center text-xs mt-2">
    *** KOT {{ $kotNumber }} ***
</div>
