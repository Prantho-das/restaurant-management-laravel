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
    private string $reportLocale = 'en';

    public function setLocale(string $locale): void
    {
        $this->reportLocale = in_array($locale, ['en', 'bn']) ? $locale : 'en';
    }

    public function getLocale(): string
    {
        return $this->reportLocale;
    }

    public function salesSummary(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
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

        $pdf = Pdf::loadView('reports.sales-summary', compact('summary', 'orders', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("sales-summary-{$startDate}-to-{$endDate}.pdf");
    }

    public function productPerformance(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
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

        $pdf = Pdf::loadView('reports.product-performance', compact('products', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("product-performance-{$startDate}-to-{$endDate}.pdf");
    }

    public function inventoryWastage(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $ingredients = Ingredient::select('ingredients.*',
            DB::raw('COALESCE(ingredients.unit_cost, 0) as estimated_cost'))
            ->get();
        $wastages = Wastage::with('ingredient')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('ingredient_id');

        $pdf = Pdf::loadView('reports.inventory-wastage', compact('ingredients', 'wastages', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("inventory-wastage-{$startDate}-to-{$endDate}.pdf");
    }

    public function profitLoss(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalExpenses = Expense::whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expensesByCategory = Expense::whereBetween('date', [$startDate, $endDate])
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
            'endDate',
            'reportLocale'
        ));

        return $pdf->download("profit-loss-{$startDate}-to-{$endDate}.pdf");
    }

    public function staffPerformance(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
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

        $pdf = Pdf::loadView('reports.staff-performance', compact('staffData', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("staff-performance-{$startDate}-to-{$endDate}.pdf");
    }

    public function purchasesReport(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
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

        $pdf = Pdf::loadView('reports.purchases', compact('purchases', 'summary', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("purchases-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function stockAdjustmentsReport(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $adjustments = StockAdjustment::with(['user', 'items.ingredient'])
            ->whereBetween('adjustment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $pdf = Pdf::loadView('reports.stock-adjustments', compact('adjustments', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("stock-adjustments-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function expensesReport(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
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

        $pdf = Pdf::loadView('reports.expenses', compact('expenses', 'summary', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("expenses-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function wastageReport(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $wastages = Wastage::with(['ingredient', 'menuItem', 'user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.wastage', compact('wastages', 'startDate', 'endDate', 'reportLocale'));

        return $pdf->download("wastage-report-{$startDate}-to-{$endDate}.pdf");
    }

    public function cashFlow(Request $request)
    {
        $this->setLocale($request->get('lang', 'en'));
        ReportHelper::setLocale($this->reportLocale);
        $reportLocale = $this->reportLocale;
        
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
            'endDate',
            'reportLocale'
        ));

        return $pdf->download("cash-flow-{$startDate}-to-{$endDate}.pdf");
    }
}
