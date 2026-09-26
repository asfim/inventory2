<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\PaymentMethod;
use App\Services\AuditLogService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index()
    {
        $investments = Investment::with(['paymentMethod', 'creator'])->orderBy('id', 'desc')->paginate(15);
        $paymentMethods = PaymentMethod::where('status', true)->get();

        $totalInvestment = Investment::sum('amount');
        
        $cashInvestment = Investment::whereHas('paymentMethod', fn($q) => $q->where('name', 'like', '%Cash%'))->sum('amount');
        $bankInvestment = Investment::whereHas('paymentMethod', fn($q) => $q->where('name', 'like', '%Bank%'))->sum('amount');
        $bkashInvestment = Investment::whereHas('paymentMethod', fn($q) => $q->where('name', 'like', '%bKash%'))->sum('amount');
        $nagadInvestment = Investment::whereHas('paymentMethod', fn($q) => $q->where('name', 'like', '%Nagad%'))->sum('amount');

        return view('investments.index', compact(
            'investments',
            'paymentMethods',
            'totalInvestment',
            'cashInvestment',
            'bankInvestment',
            'bkashInvestment',
            'nagadInvestment'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_name' => 'required|string|max:255',
            'investment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, &$investment) {
            $investment = Investment::create([
                'investor_name' => $validated['investor_name'],
                'investment_date' => $validated['investment_date'],
                'amount' => $validated['amount'],
                'payment_method_id' => $validated['payment_method_id'],
                'reference' => $validated['reference'] ?? null,
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Cash In transaction to increase selected payment method balance
            $this->ledgerService->recordPaymentTransaction(
                $validated['payment_method_id'],
                'cash_in',
                'Investment',
                $validated['amount'],
                $validated['reference'] ?? 'INV-' . $investment->id,
                "বিনিয়োগকারী: {$investment->investor_name} হতে মূলধন বিনিয়োগ"
            );
        });

        AuditLogService::log('create_investment', Investment::class, $investment->id, "নতুন মূলধন বিনিয়োগ যুক্ত করা হয়েছে: ৳{$investment->amount}");

        return back()->with('success', 'বিনিয়োগ সফলভাবে যুক্ত করা হয়েছে এবং ব্যালেন্স বৃদ্ধি পেয়েছে!');
    }
}
