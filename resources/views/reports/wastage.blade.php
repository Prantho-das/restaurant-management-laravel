@extends('reports.layout')
@section('title', 'Wastage Report')
@section('report_name', 'Wastage Report')
@section('content')
    <div class="summary-box">
        <p>This report tracks ingredient and menu item wastage registered during the period.</p>
    </div>

    <h3>Wastage Log</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th class="text-right">Est. Cost</th>
            </tr>
        </thead>
        <tbody>
            @php $totalWastageCost = 0; @endphp
            @foreach($wastages as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}</td>
                    <td>
                        @if($log->ingredient_id)
                            {{ $log->ingredient?->name ?? 'Unknown Ingredient' }}
                        @elseif($log->menu_item_id)
                            {{ $log->menuItem?->name ?? 'Unknown Menu Item' }}
                        @else
                            Unknown Item
                        @endif
                    </td>
                    <td>
                        @if($log->ingredient_id)
                            Ingredient
                        @elseif($log->menu_item_id)
                            Menu Item
                        @else
                            Unknown
                        @endif
                    </td>
                    <td>{{ $log->quantity }} {{ $log->unit }}</td>
                    <td>{{ $log->reason }}</td>
                    <td class="text-right">৳{{ number_format($log->estimated_cost, 2) }}</td>
                </tr>
                @php $totalWastageCost += $log->estimated_cost; @endphp
            @endforeach
            @if($wastages->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">No wastage recorded for this period.</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="5" class="text-right">Total Wastage Value</td>
                <td class="text-right">৳{{ number_format($totalWastageCost, 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
