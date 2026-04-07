@extends('reports.layout')
@section('title', 'Inventory & Wastage Report')
@section('report_name', 'Inventory & Wastage Report')
@section('content')
    <div class="summary-box">
        <p>This report tracks current stock levels and highlights ingredient wastage registered during the period.</p>
    </div>

    <h3>Inventory Status</h3>
    <table>
        <thead>
            <tr>
                <th>Ingredient</th>
                <th>Current Stock</th>
                <th>Alert Threshold</th>
                <th>Status</th>
                <th class="text-right">Est. Unit Cost</th>
                <th class="text-right">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @php $totalInventoryValue = 0; @endphp
            @foreach($ingredients as $ingredient)
                <tr>
                    <td>{{ $ingredient->name }}</td>
                    <td>{{ $ingredient->current_stock }} {{ $ingredient->unit }}</td>
                    <td>{{ $ingredient->alert_threshold }} {{ $ingredient->unit }}</td>
                    <td>
                        @if($ingredient->current_stock <= $ingredient->alert_threshold)
                            <span class="text-danger">LOW STOCK</span>
                        @else
                            <span class="text-success">HEALTHY</span>
                        @endif
                    </td>
                    <td class="text-right">৳{{ number_format($ingredient->estimated_cost, 2) }}</td>
                    <td class="text-right">৳{{ number_format($ingredient->current_stock * $ingredient->estimated_cost, 2) }}</td>
                </tr>
                @php $totalInventoryValue += ($ingredient->current_stock * $ingredient->estimated_cost); @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="5" class="text-right">Total Inventory Value</td>
                <td class="text-right">৳{{ number_format($totalInventoryValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="page-break"></div>

    <h3>Wastage Log</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Ingredient</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th class="text-right">Est. Cost</th>
            </tr>
        </thead>
        <tbody>
            @php $totalWastageCost = 0; @endphp
            @foreach($wastages as $ingredientId => $logs)
                @foreach($logs as $log)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}</td>
                        <td>{{ $log->ingredient?->name ?? 'Unknown/Deleted Ingredient' }}</td>
                        <td>{{ $log->quantity }} {{ $log->unit }}</td>
                        <td>{{ $log->reason }}</td>
                        <td class="text-right">৳{{ number_format($log->estimated_cost, 2) }}</td>
                    </tr>
                    @php $totalWastageCost += $log->estimated_cost; @endphp
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="4" class="text-right">Total Wastage Value</td>
                <td class="text-right">৳{{ number_format($totalWastageCost, 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
