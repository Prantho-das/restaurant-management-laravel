@extends('reports.layout')
@section('title', 'Product Performance Report')
@section('report_name', 'Menu Item Performance Report')
@section('content')
    <div class="summary-box">
        <p>This report ranks your menu items by sales volume and revenue generation for the selected period.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 50%;">Product Name</th>
                <th style="width: 15%;" class="text-right">Quantity Sold</th>
                <th style="width: 25%;" class="text-right">Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRev = 0; $totalQty = 0; @endphp
            @foreach($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">{{ number_format($product->total_quantity) }}</td>
                    <td class="text-right">৳{{ number_format($product->total_revenue, 2) }}</td>
                </tr>
                @php 
                    $totalRev += $product->total_revenue;
                    $totalQty += $product->total_quantity;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-100 font-bold">
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($totalQty) }}</td>
                <td class="text-right">৳{{ number_format($totalRev, 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
