@extends('reports.layout')

@section('title', t('discount_report'))

@section('report_name', t('discount_report'))

@section('content')
<div class="summary-box">
    <h3>{{ t('summary') }}</h3>
    <p>{{ t('total_orders') }}: {{ formatNum($summary['total_orders'], 0) }}</p>
    <p>{{ t('total_discount') }}: {{ formatNum($summary['total_discount']) }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>{{ t('order_number') }}</th>
            <th>{{ t('date') }}</th>
            <th class="text-right">{{ t('order_amount') }}</th>
            <th class="text-right">{{ t('discount_amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($discountedOrders as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ formatDate($order->created_at) }}</td>
            <td class="text-right">{{ formatNum($order->total_amount + $order->discount_amount) }}</td>
            <td class="text-right text-danger">{{ formatNum($order->discount_amount) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
