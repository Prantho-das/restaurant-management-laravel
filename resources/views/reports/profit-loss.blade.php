@extends('reports.layout')
@section('title', 'Profit & Loss Report')
@section('report_name', 'Profit & Loss Report')
@section('content')
    <div class="summary-box">
        <div class="grid">
            <div class="col">
                <p><strong>Total Revenue:</strong> ৳{{ number_format($totalRevenue, 2) }}</p>
                <p><strong>Total Expenses:</strong> (৳{{ number_format($grandTotalExpenses, 2) }})</p>
            </div>
            <div class="col font-bold">
                <p>Net Profit/Loss:</p>
                <span class="{{ $totalRevenue - $grandTotalExpenses >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 18px;">
                    ৳{{ number_format($totalRevenue - $grandTotalExpenses, 2) }}
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
            
            @if($totalPayroll > 0)
                <tr>
                    <td><strong>Staff Salaries (Payroll)</strong></td>
                    <td>Paid salaries</td>
                    <td class="text-right">৳{{ number_format($totalPayroll, 2) }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="2" class="text-right">Grand Total Expenses</td>
                <td class="text-right">৳{{ number_format($grandTotalExpenses, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <p><strong>Financial Summary:</strong></p>
        <p>Revenue: ৳{{ number_format($totalRevenue, 2) }}</p>
        <p>Regular Expenses: ৳{{ number_format($totalExpenses, 2) }}</p>
        <p>Staff Salaries: ৳{{ number_format($totalPayroll, 2) }}</p>
        <hr>
        <p><strong>Net: ৳{{ number_format($totalRevenue - $grandTotalExpenses, 2) }}</strong></p>
    </div>
@endsection
