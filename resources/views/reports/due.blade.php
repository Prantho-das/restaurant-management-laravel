@extends('reports.layout')

@section('title', t('due_report'))

@section('report_name', t('due_report'))

@section('content')
<div class="summary-box">
    <h3>{{ t('summary') }}</h3>
    <p>{{ t('total_orders') }}: {{ formatNum($dueOrders->count(), 0) }}</p>
    <p>{{ t('total_due') }}: {{ formatNum($dueOrders->sum('total_amount')) }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>{{ t('order_number') }}</th>
            <th>{{ t('customer') }}</th>
            <th>{{ t('date') }}</th>
            <th>{{ t('staff') }}</th>
            <th class="text-right">{{ t('amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dueOrders as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->customer_name ?: t('walking_customer') }}</td>
            <td>{{ formatDate($order->created_at) }}</td>
            <td>{{ $order->user?->name }}</td>
            <td class="text-right">{{ formatNum($order->total_amount) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
