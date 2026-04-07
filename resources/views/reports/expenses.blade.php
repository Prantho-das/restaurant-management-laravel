@extends('reports.layout')
@section('title', 'Expense Report')
@section('report_name', 'Expense Report')
@section('content')
    <div class="summary-box">
        <h3>Overview</h3>
        <div class="grid">
            <div class="col">
                <p><strong>Total Expenses:</strong> {{ number_format($summary['total_expenses']) }}</p>
                <p><strong>Total Cash Outflow:</strong> ৳{{ number_format($summary['total_amount'], 2) }}</p>
            </div>
            <div class="col">
                <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <h3>Expense History</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Title</th>
                <th>Payment Method</th>
                <th>Processed By</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->date->format('M d, Y') }}</td>
                    <td class="capitalize">{{ $expense->category }}</td>
                    <td>{{ $expense->title }}</td>
                    <td class="capitalize">{{ str_replace('_', ' ', $expense->payment_method) }}</td>
                    <td>{{ $expense->user->name ?? 'N/A' }}</td>
                    <td class="text-right">৳{{ number_format($expense->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No expenses found for this period.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="5" class="text-right">Grand Total</td>
                <td class="text-right">৳{{ number_format($summary['total_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
