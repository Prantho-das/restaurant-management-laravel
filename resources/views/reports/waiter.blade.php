@extends('reports.layout')

@section('title', t('waiter_report'))

@section('report_name', t('waiter_report'))

@section('content')
<div class="summary-box">
    <h3>{{ t('summary') }}</h3>
    <p>{{ t('total_waiters') }}: {{ formatNum($waiterData->count(), 0) }}</p>
    <p>{{ t('total_orders') }}: {{ formatNum($waiterData->sum('orders_count'), 0) }}</p>
    <p>{{ t('total_revenue') }}: {{ formatNum($waiterData->sum('orders_sum_total_amount')) }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>{{ t('name') }}</th>
            <th class="text-right">{{ t('orders_handled') }}</th>
            <th class="text-right">{{ t('total_sales') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($waiterData as $waiter)
        <tr>
            <td>{{ $waiter->name }}</td>
            <td class="text-right">{{ formatNum($waiter->orders_count, 0) }}</td>
            <td class="text-right">{{ formatNum($waiter->orders_sum_total_amount) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
