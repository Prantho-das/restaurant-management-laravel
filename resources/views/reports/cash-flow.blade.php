@extends('reports.layout')
@section('title', 'Cash Flow Report')
@section('report_name', 'Cash Flow Report')
@section('content')
    <div class="summary-box">
        <div class="grid">
            <div class="col">
                <p><strong>Opening Balance:</strong> ৳{{ number_format($openingBalance, 2) }}</p>
                <p><strong>Total Inflows:</strong> ৳{{ number_format($inflows, 2) }}</p>
                <p><strong>Total Outflows:</strong> ৳{{ number_format($outflows, 2) }}</p>
            </div>
            <div class="col font-bold">
                <p>Closing Balance:</p>
                <span class="{{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 18px;">
                    ৳{{ number_format($closingBalance, 2) }}
                </span>
            </div>
        </div>
    </div>

    <h3>Cash Flow Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Type</th>
                <th class="text-right">Amount (৳)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Revenue (Sales / Orders)</strong></td>
                <td><span class="text-success">Inflow</span></td>
                <td class="text-right">৳{{ number_format($inflows, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Purchases (Inventory)</strong></td>
                <td><span class="text-danger">Outflow</span></td>
                <td class="text-right">৳{{ number_format($purchases, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Operational Expenses</strong></td>
                <td><span class="text-danger">Outflow</span></td>
                <td class="text-right">৳{{ number_format($expenses, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Payroll (Salaries)</strong></td>
                <td><span class="text-danger">Outflow</span></td>
                <td class="text-right">৳{{ number_format($payroll, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="2" class="text-right">Net Cash Flow for Period</td>
                <td class="text-right {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">
                    ৳{{ number_format($netCashFlow, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <p><strong>Balance Summary:</strong></p>
        <p>Opening Balance: ৳{{ number_format($openingBalance, 2) }}</p>
        <p>Net Cash Flow: ৳{{ number_format($netCashFlow, 2) }}</p>
        <hr>
        <p><strong>Closing Balance: ৳{{ number_format($closingBalance, 2) }}</strong></p>
    </div>
@endsection
