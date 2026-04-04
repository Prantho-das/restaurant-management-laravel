@extends('reports.layout')
@section('title', 'Stock Adjustments Report')
@section('report_name', 'Stock Adjustments & Audit Report')
@section('content')
    <div class="summary-box">
        <h3>Overview</h3>
        <div class="grid">
            <div class="col">
                <p><strong>Total Adjustments:</strong> {{ number_format($adjustments->count()) }}</p>
                <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="col">
                <p><strong>Audit Trail Status:</strong> Completed Adjustments Only</p>
            </div>
        </div>
    </div>

    @forelse($adjustments as $adjustment)
        <div class="mt-4" style="border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 20px;">
            <div class="grid" style="border-bottom: 1px solid #edf2f7; padding-bottom: 5px; margin-bottom: 10px;">
                <div class="col">
                    <p><strong>Ref #:</strong> {{ $adjustment->reference_no }}</p>
                    <p><strong>Date:</strong> {{ $adjustment->adjustment_date->format('M d, Y') }}</p>
                </div>
                <div class="col text-right">
                    <p><strong>By:</strong> {{ $adjustment->user->name }}</p>
                    <p><strong>Status:</strong> <span class="text-success">{{ ucfirst($adjustment->status) }}</span></p>
                </div>
            </div>
            
            @if($adjustment->notes)
                <p style="margin-bottom: 10px;"><strong>Notes:</strong> {{ $adjustment->notes }}</p>
            @endif

            <table style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>Ingredient</th>
                        <th>Type</th>
                        <th class="text-right">Quantity</th>
                        <th>Item Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adjustment->items as $item)
                        <tr>
                            <td>{{ $item->ingredient->name }}</td>
                            <td>
                                <span class="{{ $item->type === 'add' ? 'text-success' : 'text-danger' }}">
                                    {{ $item->type === 'add' ? 'Addition' : 'Subtraction' }}
                                </span>
                            </td>
                            <td class="text-right">
                                {{ number_format($item->quantity, 2) }} {{ $item->ingredient->unit }}
                            </td>
                            <td>{{ $item->note ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="text-center mt-4">
            <p>No stock adjustments found for this period.</p>
        </div>
    @endforelse
@endsection
