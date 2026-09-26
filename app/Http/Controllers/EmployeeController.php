<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\PaymentMethod;
use App\Services\AuditLogService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index()
    {
        $employees = Employee::orderBy('id', 'desc')->get();
        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'designation' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
        ]);

        Employee::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'designation' => $validated['designation'],
            'joining_date' => $validated['joining_date'],
            'salary' => $validated['salary'],
            'status' => true,
        ]);

        return redirect()->route('employees.index')->with('success', 'কর্মচারী সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function show(Employee $employee)
    {
        $employee->load('salaries.paymentMethod');
        $paymentMethods = PaymentMethod::where('status', true)->get();
        return view('employees.show', compact('employee', 'paymentMethods'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'designation' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        $employee->update($validated);
        return redirect()->route('employees.index')->with('success', 'কর্মচারী তথ্য আপডেট করা হয়েছে!');
    }

    public function paySalary(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'month_year' => 'required|string|max:50',
            'salary_amount' => 'required|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($employee, $validated) {
            $paidAmount = $validated['paid_amount'];
            $dueAmount = max(0, $validated['salary_amount'] - ($validated['advance_amount'] ?? 0) - $paidAmount);

            EmployeeSalary::create([
                'employee_id' => $employee->id,
                'payment_date' => $validated['payment_date'],
                'month_year' => $validated['month_year'],
                'salary_amount' => $validated['salary_amount'],
                'advance_amount' => $validated['advance_amount'] ?? 0,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Cash Out record
            $this->ledgerService->recordPaymentTransaction(
                $validated['payment_method_id'],
                'cash_out',
                'Salary',
                $paidAmount,
                'SAL-' . $employee->id . '-' . time(),
                "কর্মচারী: {$employee->name} ({$validated['month_year']}) বেতন পরিষদ"
            );
        });

        AuditLogService::log('pay_salary', Employee::class, $employee->id, "কর্মচারীকে বেতন পরিশোধ করা হয়েছে: ৳{$validated['paid_amount']}");

        return back()->with('success', 'কর্মচারীর বেতন পরিষদ এন্ট্রি সম্পন্ন হয়েছে!');
    }
}
