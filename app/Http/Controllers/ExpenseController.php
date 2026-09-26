<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Services\AuditLogService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index(Request $request)
    {
        $query = Expense::with(['category', 'paymentMethod', 'creator']);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->orderBy('id', 'desc')->paginate(15);
        $categories = ExpenseCategory::where('status', true)->get();
        $paymentMethods = PaymentMethod::where('status', true)->get();
        $totalExpense = Expense::sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'paymentMethods', 'totalExpense'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        DB::transaction(function () use ($request, $validated, &$expense) {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('expenses', 'public');
            }

            $expense = Expense::create([
                'expense_date' => $validated['expense_date'],
                'expense_category_id' => $validated['expense_category_id'],
                'amount' => $validated['amount'],
                'payment_method_id' => $validated['payment_method_id'],
                'description' => $validated['description'] ?? null,
                'attachment' => $attachmentPath,
                'created_by' => auth()->id(),
            ]);

            // Cash Out record & payment method balance decrement
            $this->ledgerService->recordPaymentTransaction(
                $validated['payment_method_id'],
                'cash_out',
                'Expense',
                $validated['amount'],
                'EXP-' . $expense->id,
                "খরচ: {$expense->category->name} - " . ($validated['description'] ?? '')
            );
        });

        AuditLogService::log('create_expense', Expense::class, $expense->id, "নতুন খরচ যুক্ত করা হয়েছে: ৳{$expense->amount}");

        return back()->with('success', 'খরচ এন্ট্রি সফলভাবে সম্পন্ন হয়েছে!');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        ExpenseCategory::create([
            'name' => $validated['name'],
            'status' => true,
        ]);

        return back()->with('success', 'নতুন খরচের খাত যুক্ত করা হয়েছে!');
    }
}
