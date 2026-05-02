@extends('reports.layout')
@section('title', 'Purchases Report')
@section('report_name', 'Inventory Purchases Report')
@section('content')
    <div class="summary-box">
        <h3>Overview</h3>
        <div class="grid">
            <div class="col">
                <p><strong>Total Purchases:</strong> {{ number_format($summary['total_purchases']) }}</p>
                <p><strong>Total Investment:</strong> ৳{{ number_format($summary['total_amount'], 2) }}</p>
            </div>
            <div class="col">
                <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <h3>Purchase History</h3>
    <table>
        <thead>
            <tr>
                <th>Ref #</th>
                <th>Date</th>
                <th>Supplier</th>
                <th>Processed By</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->reference_no }}</td>
                    <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                    <td>{{ $purchase->supplier?->name ?? 'N/A' }}</td>
                    <td>{{ $purchase->user?->name ?? 'N/A' }}</td>
                    <td class="text-right">৳{{ number_format($purchase->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No purchases found for this period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="4" class="text-right">Grand Total</td>
                <td class="text-right">৳{{ number_format($summary['total_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
