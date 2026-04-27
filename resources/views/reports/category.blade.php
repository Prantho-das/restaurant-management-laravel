@extends('reports.layout')

@section('title', t('category_report'))

@section('report_name', t('category_report'))

@section('content')
<div class="summary-box">
    <h3>{{ t('summary') }}</h3>
    <div class="grid">
        <div class="col">
            <p>{{ t('total_categories') }}: {{ formatNum($categories->count(), 0) }}</p>
            <p>{{ t('total_revenue') }}: {{ formatNum($categories->sum('total_revenue')) }}</p>
        </div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>{{ t('category') }}</th>
            <th class="text-right">{{ t('items_sold') }}</th>
            <th class="text-right">{{ t('revenue') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td class="text-right">{{ formatNum($category->total_quantity, 0) }}</td>
            <td class="text-right">{{ formatNum($category->total_revenue) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
