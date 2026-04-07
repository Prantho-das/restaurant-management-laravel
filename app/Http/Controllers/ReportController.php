<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\StockAdjustment;
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

        $latestPurchases = DB::table('purchase_items')
            ->select('ingredient_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('ingredient_id');

        $ingredients = Ingredient::leftJoinSub($latestPurchases, 'latest_purchases', function ($join) {
            $join->on('ingredients.id', '=', 'latest_purchases.ingredient_id');
        })
            ->leftJoin('purchase_items', 'purchase_items.id', '=', 'latest_purchases.max_id')
            ->select('ingredients.*', DB::raw('COALESCE(purchase_items.unit_price, 0) as estimated_cost'))
            ->get();
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

        $totalPayroll = Payroll::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('net_paid');

        // Combined total for the summary
        $grandTotalExpenses = $totalExpenses + $totalPayroll;

        $pdf = Pdf::loadView('reports.profit-loss', compact(
            'totalRevenue',
            'totalExpenses',
            'expensesByCategory',
            'totalPayroll',
            'grandTotalExpenses',
            'startDate',
            'endDate'
        ));

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

    public function purchasesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $purchases = Purchase::with(['supplier', 'user'])
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', 'received')
            ->get();

        $summary = [
            'total_amount' => $purchases->sum('total_amount'),
            'total_purchases' => $purchases->count(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $pdf = Pdf::loadView('reports.purchases', compact('purchases', 'summary', 'startDate', 'endDate'));

        return $pdf->download("purchases-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function stockAdjustmentsReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $adjustments = StockAdjustment::with(['user', 'items.ingredient'])
            ->whereBetween('adjustment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $pdf = Pdf::loadView('reports.stock-adjustments', compact('adjustments', 'startDate', 'endDate'));

        return $pdf->download("stock-adjustments-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function expensesReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $expenses = Expense::with(['user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'total_amount' => $expenses->sum('amount'),
            'total_expenses' => $expenses->count(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $pdf = Pdf::loadView('reports.expenses', compact('expenses', 'summary', 'startDate', 'endDate'));

        return $pdf->download("expenses-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function wastageReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $wastages = Wastage::with(['ingredient', 'menuItem', 'user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.wastage', compact('wastages', 'startDate', 'endDate'));

        return $pdf->download("wastage-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Opening Balance Calculation (everything before start_date)
        $openingInflows = Order::where('created_at', '<', $startDate)
            ->where('status', 'completed')
            ->sum('total_amount');

        $openingPurchases = Purchase::where('purchase_date', '<', $startDate)
            ->where('status', 'received')
            ->sum('total_amount');

        $openingExpenses = Expense::where('date', '<', $startDate)
            ->sum('amount');

        $openingPayroll = Payroll::where('payment_date', '<', $startDate)
            ->where('status', 'paid')
            ->sum('net_paid');

        $openingOutflows = $openingPurchases + $openingExpenses + $openingPayroll;
        $openingBalance = $openingInflows - $openingOutflows;

        // Current Period Inflows
        $inflows = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Current Period Outflows
        $purchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', 'received')
            ->sum('total_amount');

        $expenses = Expense::whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $payroll = Payroll::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('net_paid');

        $outflows = $purchases + $expenses + $payroll;
        $netCashFlow = $inflows - $outflows;
        $closingBalance = $openingBalance + $netCashFlow;

        $pdf = Pdf::loadView('reports.cash-flow', compact(
            'openingBalance',
            'inflows',
            'purchases',
            'expenses',
            'payroll',
            'outflows',
            'netCashFlow',
            'closingBalance',
            'startDate',
            'endDate'
        ));

        return $pdf->download("cash-flow-{$startDate}-to-{$endDate}.pdf");
    }
}
