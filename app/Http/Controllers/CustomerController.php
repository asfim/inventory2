<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Services\AuditLogService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index()
    {
        $customers = Customer::withCount('sales')->orderBy('id', 'desc')->get();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_due' => 'nullable|numeric|min:0',
        ]);

        $openingDue = $validated['opening_due'] ?? 0;

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'opening_due' => $openingDue,
            'current_due' => $openingDue,
            'status' => true,
        ]);

        if ($openingDue > 0) {
            $this->ledgerService->recordCustomerTransaction(
                $customer->id,
                'opening_due',
                'INIT-DUE',
                $openingDue,
                0,
                'প্রাথমিক ওপেনিং বাকি'
            );
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return redirect()->route('customers.index')->with('success', 'গ্রাহক সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function show(Customer $customer)
    {
        $customer->load(['ledgers' => function ($q) {
            $q->orderBy('date', 'desc')->orderBy('id', 'desc');
        }, 'sales']);

        $paymentMethods = PaymentMethod::where('status', true)->get();
        return view('customers.show', compact('customer', 'paymentMethods'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'গ্রাহকের তথ্য আপডেট করা হয়েছে!');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->count() > 0) {
            return back()->with('error', 'এই গ্রাহকের সেলস হিস্ট্রি থাকায় মুছে ফেলা সম্ভব নয়!');
        }
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'গ্রাহক মুছে ফেলা হয়েছে!');
    }

    public function addPayment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $amount = $validated['amount'];
            $paymentMethodId = $validated['payment_method_id'];

            // 1. Customer ledger update (Customer paid => Credit)
            $this->ledgerService->recordCustomerTransaction(
                $customer->id,
                'payment',
                'CUST-PAY-' . time(),
                0,
                $amount,
                $validated['note'] ?? 'গ্রাহক থেকে বকেয়া আদায়'
            );

            // 2. Cash In payment transaction
            $this->ledgerService->recordPaymentTransaction(
                $paymentMethodId,
                'cash_in',
                'Customer Payment',
                $amount,
                'CUST-PAY-' . $customer->id,
                "গ্রাহক: {$customer->name} থেকে আদায়"
            );
        });

        AuditLogService::log('customer_payment', Customer::class, $customer->id, "গ্রাহক থেকে ৳{$validated['amount']} আদায় করা হয়েছে");

        return back()->with('success', 'গ্রাহক বকেয়া আদায় এন্ট্রি সফল হয়েছে!');
    }
}
