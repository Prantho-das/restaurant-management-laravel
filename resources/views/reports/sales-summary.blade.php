@extends('reports.layout')
@section('title', t('sales_summary_report'))
@section('report_name', t('sales_summary_report'))
@section('content')
    <div class="summary-box">
        <h3>{{ t('overview') }}</h3>
        <div class="grid">
            <div class="col">
                <p><strong>{{ t('total_orders') }}:</strong> {{ formatNum($summary['total_orders'], 0) }}</p>
                <p><strong>{{ t('total_revenue') }}:</strong> ৳{{ formatNum($summary['total_revenue']) }}</p>
                <p><strong>{{ t('average_order_value') }}:</strong> ৳{{ formatNum($summary['avg_order_value']) }}</p>
            </div>
            <div class="col">
                <p><strong>{{ t('payment_methods') }}:</strong></p>
                @foreach($summary['payment_methods'] as $method => $count)
                    <p>{{ tm($method) }}: {{ formatNum($count, 0) }}</p>
                @endforeach
            </div>
        </div>
    </div>

    <h3>{{ t('order_breakdown') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ t('order_number') }}</th>
                <th>{{ t('date') }}</th>
                <th>{{ t('type') }}</th>
                <th>{{ t('payment') }}</th>
                <th class="text-right">{{ t('amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ formatDate($order->created_at, 'M d, Y H:i') }}</td>
                    <td>{{ tt($order->order_type) }}</td>
                    <td>{{ tm($order->payment_method) }}</td>
                    <td class="text-right">৳{{ formatNum($order->total_amount) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="4" class="text-right">{{ t('grand_total') }}</td>
                <td class="text-right">৳{{ formatNum($summary['total_revenue']) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
