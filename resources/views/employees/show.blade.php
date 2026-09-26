@extends('layouts.app')

@section('title', $employee->name . ' - বেতন ও প্রোফাইল')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-user-tie me-2"></i>{{ $employee->name }}</h4>
        <p class="text-muted mb-0 fs-7">{{ $employee->designation }} | {{ $employee->phone }} | {{ lang('বেতন:', 'Salary:') }} {{ format_currency($employee->salary) }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paySalaryModal">
            <i class="fa-solid fa-money-bill-wave me-1"></i> {{ lang('বেতন প্রদান করুন', 'Pay Salary') }}
        </button>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-file-invoice-dollar text-success me-2"></i>{{ lang('বেতন পরিষদের ইতিহাস (Salary History)', 'Salary Disbursement History') }}</span>
        <button class="btn btn-sm btn-outline-secondary no-print" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট', 'Print') }}</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('মাস ও বছর', 'Month & Year') }}</th>
                        <th>{{ lang('পরিশোধের তারিখ', 'Payment Date') }}</th>
                        <th>{{ lang('ধার্য বেতন', 'Salary Amount') }}</th>
                        <th>{{ lang('অগ্রিম (Advance)', 'Advance') }}</th>
                        <th>{{ lang('পরিশোধিত', 'Paid Amount') }}</th>
                        <th>{{ lang('বকেয়া', 'Due Amount') }}</th>
                        <th>{{ lang('পেমেন্ট মেথড', 'Method') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($employee->salaries as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $s->month_year }}</td>
                            <td>{{ $s->payment_date->format('d M, Y') }}</td>
                            <td>{{ format_currency($s->salary_amount) }}</td>
                            <td class="text-danger">{{ format_currency($s->advance_amount) }}</td>
                            <td class="text-success fw-bold">{{ format_currency($s->paid_amount) }}</td>
                            <td class="text-danger fw-bold">{{ format_currency($s->due_amount) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $s->paymentMethod->name ?? 'N/A' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">{{ lang('কোন বেতন হিস্ট্রি পাওয়া যায়নি', 'No salary disbursement history found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pay Salary Modal -->
<div class="modal fade" id="paySalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('employees.salary', $employee->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('কর্মচারীর বেতন প্রদান এন্ট্রি', 'Pay Employee Salary') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('মাস ও বছর (Month & Year)', 'Month & Year') }} <span class="text-danger">*</span></label>
                        <input type="text" name="month_year" class="form-control" value="{{ date('F Y') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পরিশোধের তারিখ', 'Payment Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ধার্য বেতন (Salary ৳)', 'Salary Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="salary_amount" class="form-control" value="{{ $employee->salary }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('অগ্রিম সমন্বয় (Advance Deduction ৳)', 'Advance Deduction') }}</label>
                        <input type="number" step="0.01" name="advance_amount" class="form-control" value="0.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('প্রদেয় পরিমাণ (Paid Amount ৳)', 'Paid Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="paid_amount" class="form-control fw-bold text-success fs-5" value="{{ $employee->salary }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পেমেন্ট মেথড (Payment Method)', 'Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method_id" class="form-select" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ format_currency($pm->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('নোট', 'Note') }}</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="নোট লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">বেতন পরিশোধ কনফার্ম করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
