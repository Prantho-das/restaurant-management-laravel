<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\User;
use App\Models\Wastage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $summary = [
            'total_revenue' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum('total_amount') / $orders->count() : 0,
            'payment_methods' => $orders->groupBy('payment_method')->map->count(),
            'order_types' => $orders->groupBy('order_type')->map->count(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $pdf = Pdf::loadView('reports.sales-summary', compact('summary', 'orders', 'startDate', 'endDate'));

        return $pdf->download("sales-summary-{$startDate}-to-{$endDate}.pdf");
    }

    public function productPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $products = DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->select(
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_quantity')
            ->get();

        $pdf = Pdf::loadView('reports.product-performance', compact('products', 'startDate', 'endDate'));

        return $pdf->download("product-performance-{$startDate}-to-{$endDate}.pdf");
    }

    public function inventoryWastage(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $ingredients = Ingredient::all();
        $wastages = Wastage::with('ingredient')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('ingredient_id');

        $pdf = Pdf::loadView('reports.inventory-wastage', compact('ingredients', 'wastages', 'startDate', 'endDate'));

        return $pdf->download("inventory-wastage-{$startDate}-to-{$endDate}.pdf");
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $expensesByCategory = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy('category');

        $pdf = Pdf::loadView('reports.profit-loss', compact('totalRevenue', 'totalExpenses', 'expensesByCategory', 'startDate', 'endDate'));

        return $pdf->download("profit-loss-{$startDate}-to-{$endDate}.pdf");
    }

    public function staffPerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $staffData = User::withCount(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed');
        }])
            ->withSum(['orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed');
            }], 'total_amount')
            ->get()
            ->filter(fn ($user) => $user->orders_count > 0);

        $pdf = Pdf::loadView('reports.staff-performance', compact('staffData', 'startDate', 'endDate'));

        return $pdf->download("staff-performance-{$startDate}-to-{$endDate}.pdf");
    }
}
