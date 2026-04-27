@extends('reports.layout')

@section('title', t('channel_report'))

@section('report_name', t('channel_report'))

@section('content')
<div class="summary-box">
    <h3>{{ t('summary') }}</h3>
    <p>{{ t('total_revenue') }}: {{ formatNum($channels->sum('total_revenue')) }}</p>
    <p>{{ t('total_orders') }}: {{ formatNum($channels->sum('total_orders'), 0) }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>{{ t('channel') }}</th>
            <th class="text-right">{{ t('orders') }}</th>
            <th class="text-right">{{ t('revenue') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($channels as $channel)
        <tr>
            <td>{{ tt($channel->order_type) }}</td>
            <td class="text-right">{{ formatNum($channel->total_orders, 0) }}</td>
            <td class="text-right">{{ formatNum($channel->total_revenue) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
