@extends('reports.layout')
@section('title', 'Sales Summary Report')
@section('report_name', 'Sales Summary Report')
@section('content')
    <div class="summary-box">
        <h3>Overview</h3>
        <div class="grid">
            <div class="col">
                <p><strong>Total Orders:</strong> {{ number_format($summary['total_orders']) }}</p>
                <p><strong>Total Revenue:</strong> ৳{{ number_format($summary['total_revenue'], 2) }}</p>
                <p><strong>Average Order Value:</strong> ৳{{ number_format($summary['avg_order_value'], 2) }}</p>
            </div>
            <div class="col">
                <p><strong>Payment Methods:</strong></p>
                @foreach($summary['payment_methods'] as $method => $count)
                    <p>{{ ucfirst($method) }}: {{ $count }}</p>
                @endforeach
            </div>
        </div>
    </div>

    <h3>Order Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Type</th>
                <th>Payment</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td>{{ ucfirst($order->order_type) }}</td>
                    <td>{{ ucfirst($order->payment_method) }}</td>
                    <td class="text-right">৳{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="4" class="text-right">Grand Total</td>
                <td class="text-right">৳{{ number_format($summary['total_revenue'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
