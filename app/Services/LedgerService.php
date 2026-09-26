<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\PaymentMethod;
use App\Models\CashTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LedgerService
{
    /**
     * Record transaction for Supplier Ledger & update current due
     */
    public function recordSupplierTransaction(int $supplierId, string $type, ?string $reference, float $debit, float $credit, ?string $note = null): void
    {
        DB::transaction(function () use ($supplierId, $type, $reference, $debit, $credit, $note) {
            $supplier = Supplier::findOrFail($supplierId);
            
            // balance calculation: current_due + credit (purchases/due added) - debit (payments/discounts)
            $newDue = max(0, $supplier->current_due + $credit - $debit);
            $supplier->update(['current_due' => $newDue]);

            SupplierLedger::create([
                'supplier_id' => $supplierId,
                'date' => now()->toDateString(),
                'type' => $type,
                'reference' => $reference,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $newDue,
                'note' => $note,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Record transaction for Customer Ledger & update current due
     */
    public function recordCustomerTransaction(int $customerId, string $type, ?string $reference, float $debit, float $credit, ?string $note = null): void
    {
        DB::transaction(function () use ($customerId, $type, $reference, $debit, $credit, $note) {
            $customer = Customer::findOrFail($customerId);

            // balance calculation: current_due + debit (sales/due added) - credit (payments/returns)
            $newDue = max(0, $customer->current_due + $debit - $credit);
            $customer->update(['current_due' => $newDue]);

            CustomerLedger::create([
                'customer_id' => $customerId,
                'date' => now()->toDateString(),
                'type' => $type,
                'reference' => $reference,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $newDue,
                'note' => $note,
                'created_by' => Auth::id(),
            ]);
        });
    }

    /**
     * Record transaction for Payment Method & Cash Book
     */
    public function recordPaymentTransaction(int $paymentMethodId, string $transactionType, string $category, float $amount, ?string $reference = null, ?string $description = null): void
    {
        if ($amount <= 0) return;

        DB::transaction(function () use ($paymentMethodId, $transactionType, $category, $amount, $reference, $description) {
            $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

            if ($transactionType === 'cash_in') {
                $paymentMethod->increment('current_balance', $amount);
            } else {
                $paymentMethod->decrement('current_balance', $amount);
            }

            CashTransaction::create([
                'date' => now()->toDateString(),
                'type' => $transactionType,
                'category' => $category,
                'amount' => $amount,
                'payment_method_id' => $paymentMethodId,
                'reference' => $reference,
                'description' => $description,
                'created_by' => Auth::id(),
            ]);
        });
    }
}
