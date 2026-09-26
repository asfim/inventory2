<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::withCount('cashTransactions')->get();
        return view('payment_methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'account_holder' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        PaymentMethod::create([
            'name' => $validated['name'],
            'account_number' => $validated['account_number'] ?? null,
            'account_holder' => $validated['account_holder'] ?? null,
            'opening_balance' => $validated['opening_balance'],
            'current_balance' => $validated['opening_balance'],
            'status' => true,
        ]);

        return back()->with('success', 'নতুন পেমেন্ট মেথড যুক্ত করা হয়েছে!');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $transactions = CashTransaction::where('payment_method_id', $paymentMethod->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('payment_methods.show', compact('paymentMethod', 'transactions'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'account_holder' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $paymentMethod->update($validated);
        return back()->with('success', 'পেমেন্ট মেথড তথ্য আপডেট করা হয়েছে!');
    }
}
