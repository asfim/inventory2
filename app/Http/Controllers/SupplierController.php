<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Supplier;
use App\Services\AuditLogService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->orderBy('id', 'desc')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_due' => 'nullable|numeric|min:0',
        ]);

        $openingDue = $validated['opening_due'] ?? 0;

        $supplier = Supplier::create([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'opening_due' => $openingDue,
            'current_due' => $openingDue,
            'status' => true,
        ]);

        if ($openingDue > 0) {
            $this->ledgerService->recordSupplierTransaction(
                $supplier->id,
                'opening_due',
                'INIT-DUE',
                0,
                $openingDue,
                'প্রাথমিক ওপেনিং বাকি'
            );
        }

        return redirect()->route('suppliers.index')->with('success', 'সরবরাহকারী সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['ledgers' => function ($q) {
            $q->orderBy('date', 'desc')->orderBy('id', 'desc');
        }, 'purchases']);
        
        $paymentMethods = PaymentMethod::where('status', true)->get();
        return view('suppliers.show', compact('supplier', 'paymentMethods'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'সরবরাহকারী তথ্য আপডেট করা হয়েছে!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', 'এই সরবরাহকারীর পারচেজ হিস্ট্রি থাকায় মুছে ফেলা সম্ভব নয়!');
        }
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'সরবরাহকারী মুছে ফেলা হয়েছে!');
    }

    public function addPayment(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($supplier, $validated) {
            $amount = $validated['amount'];
            $paymentMethodId = $validated['payment_method_id'];

            // 1. Supplier ledger update (Paid to supplier => Debit)
            $this->ledgerService->recordSupplierTransaction(
                $supplier->id,
                'payment',
                'SUP-PAY-' . time(),
                $amount,
                0,
                $validated['note'] ?? 'সরবরাহকারীকে বকেয়া পরিশোধ'
            );

            // 2. Cash Out payment transaction
            $this->ledgerService->recordPaymentTransaction(
                $paymentMethodId,
                'cash_out',
                'Supplier Payment',
                $amount,
                'SUP-PAY-' . $supplier->id,
                "সরবরাহকারী: {$supplier->name}-কে পরিশোধ"
            );
        });

        AuditLogService::log('supplier_payment', Supplier::class, $supplier->id, "সরবরাহকারীকে ৳{$validated['amount']} পরিশোধ করা হয়েছে");

        return back()->with('success', 'সরবরাহকারীর বকেয়া পরিষদ এন্ট্রি সফল হয়েছে!');
    }
}
