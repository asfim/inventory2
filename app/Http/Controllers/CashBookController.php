<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\PaymentMethod;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashBookController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        $transactions = CashTransaction::with(['paymentMethod', 'creator'])
            ->whereDate('date', $date)
            ->orderBy('id', 'asc')
            ->get();

        $openingBalance = CashTransaction::whereDate('date', '<', $date)
            ->selectRaw("SUM(CASE WHEN type='cash_in' THEN amount ELSE -amount END) as net_balance")
            ->value('net_balance') ?? 0;

        // Also add initial payment method opening balances
        $initialOpening = PaymentMethod::sum('opening_balance');
        $totalOpeningBalance = $openingBalance + $initialOpening;

        $totalCashIn = $transactions->where('type', 'cash_in')->sum('amount');
        $totalCashOut = $transactions->where('type', 'cash_out')->sum('amount');
        $closingBalance = $totalOpeningBalance + $totalCashIn - $totalCashOut;

        $paymentMethods = PaymentMethod::where('status', true)->get();

        return view('cashbook.index', compact(
            'transactions',
            'date',
            'totalOpeningBalance',
            'totalCashIn',
            'totalCashOut',
            'closingBalance',
            'paymentMethods'
        ));
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:cash_in,cash_out',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $this->ledgerService->recordPaymentTransaction(
            $validated['payment_method_id'],
            $validated['type'],
            $validated['category'],
            $validated['amount'],
            $validated['reference'] ?? 'MANUAL',
            $validated['description'] ?? null
        );

        return back()->with('success', 'ক্যাশ বুক এন্ট্রি সফলভাবে সম্পন্ন হয়েছে!');
    }
}
