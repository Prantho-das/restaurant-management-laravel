@extends('reports.layout')
@section('title', 'Profit & Loss Report')
@section('report_name', 'Profit & Loss Report')
@section('content')
    <div class="summary-box">
        <div class="grid">
            <div class="col">
                <p><strong>Total Revenue:</strong> ৳{{ number_format($totalRevenue, 2) }}</p>
                <p><strong>Total Expenses:</strong> (৳{{ number_format($totalExpenses, 2) }})</p>
            </div>
            <div class="col font-bold">
                <p>Net Profit/Loss:</p>
                <span class="{{ $totalRevenue - $totalExpenses >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 18px;">
                    ৳{{ number_format($totalRevenue - $totalExpenses, 2) }}
                </span>
            </div>
        </div>
    </div>

    <h3>Expense Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Description / Count</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expensesByCategory as $category => $items)
                <tr>
                    <td><strong>{{ ucfirst($category) }}</strong></td>
                    <td>{{ $items->count() }} line items</td>
                    <td class="text-right">৳{{ number_format($items->sum('amount'), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="2" class="text-right">Total Expenses</td>
                <td class="text-right">৳{{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <p><strong>Financial Summary:</strong></p>
        <p>Revenue: ৳{{ number_format($totalRevenue, 2) }}</p>
        <p>Expenses: ৳{{ number_format($totalExpenses, 2) }}</p>
        <hr>
        <p><strong>Net: ৳{{ number_format($totalRevenue - $totalExpenses, 2) }}</strong></p>
    </div>
@endsection
