<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Investment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $customerId = $request->get('customer_id');
        $paymentMethodId = $request->get('payment_method_id');

        $query = Sale::with(['customer', 'paymentMethod', 'seller', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate]);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($paymentMethodId) {
            $query->where('payment_method_id', $paymentMethodId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $totalSalesAmount = $sales->sum('net_amount');
        $totalPaidAmount = $sales->sum('paid_amount');
        $totalDueAmount = $sales->sum('due_amount');

        $customers = Customer::where('status', true)->get();
        $paymentMethods = PaymentMethod::where('status', true)->get();

        return view('reports.sales', compact(
            'sales',
            'startDate',
            'endDate',
            'totalSalesAmount',
            'totalPaidAmount',
            'totalDueAmount',
            'customers',
            'paymentMethods'
        ));
    }

    public function purchaseReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $supplierId = $request->get('supplier_id');

        $query = Purchase::with(['supplier', 'paymentMethod', 'items.product'])
            ->whereBetween('purchase_date', [$startDate, $endDate]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();
        $totalPurchaseAmount = $purchases->sum('net_amount');
        $totalPaidAmount = $purchases->sum('paid_amount');
        $totalDueAmount = $purchases->sum('due_amount');

        $suppliers = Supplier::where('status', true)->get();

        return view('reports.purchases', compact(
            'purchases',
            'startDate',
            'endDate',
            'totalPurchaseAmount',
            'totalPaidAmount',
            'totalDueAmount',
            'suppliers'
        ));
    }

    public function stockReport(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = Product::with(['category', 'brand', 'unit', 'batches']);

        if ($filter === 'low') {
            $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
        } elseif ($filter === 'out') {
            $query->where('current_stock', '<=', 0);
        }

        $products = $query->get();
        $totalStockValue = $products->sum(fn($p) => $p->current_stock * $p->purchase_price);
        $totalSellingValue = $products->sum(fn($p) => $p->current_stock * $p->selling_price);

        return view('reports.stock', compact('products', 'filter', 'totalStockValue', 'totalSellingValue'));
    }

    public function financialReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $totalSales = Sale::whereBetween('sale_date', [$startDate, $endDate])->sum('net_amount');
        $totalPurchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])->sum('net_amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $totalInvestments = Investment::whereBetween('investment_date', [$startDate, $endDate])->sum('amount');

        // COGS estimation for date range
        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->selectRaw('SUM(sale_items.quantity * products.purchase_price) as total_cogs')
            ->value('total_cogs') ?? 0;

        $grossProfit = $totalSales - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        $totalCustomerDue = Customer::sum('current_due');
        $totalSupplierDue = Supplier::sum('current_due');

        $paymentMethods = PaymentMethod::all();

        return view('reports.financial', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'totalInvestments',
            'cogs',
            'grossProfit',
            'netProfit',
            'totalCustomerDue',
            'totalSupplierDue',
            'paymentMethods'
        ));
    }
}
