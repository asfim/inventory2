<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Financial & Sales Totals
        $todaySales = Sale::whereDate('sale_date', $today)->sum('net_amount');
        $totalSales = Sale::sum('net_amount');
        $todayPurchase = Purchase::whereDate('purchase_date', $today)->sum('net_amount');
        $totalPurchase = Purchase::sum('net_amount');
        $totalExpense = Expense::sum('amount');
        
        // Balances
        $cashMethod = PaymentMethod::where('name', 'like', '%Cash%')->first();
        $bankMethod = PaymentMethod::where('name', 'like', '%Bank%')->first();
        $bkashMethod = PaymentMethod::where('name', 'like', '%bKash%')->first();
        $nagadMethod = PaymentMethod::where('name', 'like', '%Nagad%')->first();

        $cashBalance = $cashMethod ? $cashMethod->current_balance : 0;
        $bankBalance = $bankMethod ? $bankMethod->current_balance : 0;
        $bkashBalance = $bkashMethod ? $bkashMethod->current_balance : 0;
        $nagadBalance = $nagadMethod ? $nagadMethod->current_balance : 0;

        $totalCustomerDue = Customer::sum('current_due');
        $totalSupplierDue = Supplier::sum('current_due');

        // Stock Metrics
        $products = Product::all();
        $currentStockValue = $products->sum(fn($p) => $p->current_stock * $p->purchase_price);

        $lowStockProducts = Product::with(['category', 'unit'])
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->get();
        $lowStockCount = $lowStockProducts->count();

        // Expiry Metrics
        $allBatches = ProductBatch::with('product')->where('quantity', '>', 0)->get();
        $expiredCount = $allBatches->filter(fn($b) => $b->days_until_expiry < 0)->count();
        $nearExpiryCount = $allBatches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 60)->count();

        $nearExpiryList = $allBatches->filter(fn($b) => $b->days_until_expiry <= 60)->take(5);

        // Recent Records
        $recentSales = Sale::with(['customer', 'paymentMethod'])->orderBy('id', 'desc')->take(5)->get();
        $recentPurchases = Purchase::with(['supplier', 'paymentMethod'])->orderBy('id', 'desc')->take(5)->get();

        // Estimated Net Profit = Total Sales - Cost of Goods Sold (using purchase price) - Total Expenses
        // COGS estimation for demo/dashboard summary:
        $totalCogs = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(sale_items.quantity * products.purchase_price) as total_cogs')
            ->value('total_cogs') ?? 0;

        $netProfit = $totalSales - $totalCogs - $totalExpense;

        // Chart Data (Last 6 Months Sales & Purchases)
        $months = [];
        $salesChartData = [];
        $purchaseChartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->format('M Y');
            $months[] = $monthName;

            $sAmount = Sale::whereYear('sale_date', $monthDate->year)
                ->whereMonth('sale_date', $monthDate->month)
                ->sum('net_amount');

            $pAmount = Purchase::whereYear('purchase_date', $monthDate->year)
                ->whereMonth('purchase_date', $monthDate->month)
                ->sum('net_amount');

            $salesChartData[] = (float) $sAmount;
            $purchaseChartData[] = (float) $pAmount;
        }

        // Payment Method breakdown chart data
        $paymentMethods = PaymentMethod::where('status', true)->get();
        $pmLabels = $paymentMethods->pluck('name')->toArray();
        $pmBalances = $paymentMethods->pluck('current_balance')->toArray();

        return view('dashboard.index', compact(
            'todaySales',
            'totalSales',
            'todayPurchase',
            'totalPurchase',
            'totalExpense',
            'currentStockValue',
            'cashBalance',
            'bankBalance',
            'bkashBalance',
            'nagadBalance',
            'totalCustomerDue',
            'totalSupplierDue',
            'lowStockCount',
            'expiredCount',
            'nearExpiryCount',
            'netProfit',
            'lowStockProducts',
            'nearExpiryList',
            'recentSales',
            'recentPurchases',
            'months',
            'salesChartData',
            'purchaseChartData',
            'pmLabels',
            'pmBalances'
        ));
    }
}
