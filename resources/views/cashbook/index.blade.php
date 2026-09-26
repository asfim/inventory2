@extends('layouts.app')

@section('title', lang('ক্যাশ বুক', 'Cash Book'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-book-journal-whills me-2"></i>{{ lang('ক্যাশ বুক (Daily Cash Book Ledger)', 'Daily Cash Book') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দৈনিক ক্যাশ ইন ও ক্যাশ আউট হিসাব, প্রারম্ভিক ও সমাপনী ব্যালেন্স ট্রেকিং', 'Daily Cash In & Cash Out transactions, opening balance & closing balance tracking') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCashModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('ম্যানুয়াল ক্যাশ এন্ট্রি', 'Manual Cash Entry') }}
    </button>
</div>

<!-- Date Filter & Daily Summary Strip -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('cashbook.index') }}" method="GET" class="row g-3 align-items-center mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold fs-7">{{ lang('তারিখ নির্বাচন করুন', 'Select Date') }}</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-8 text-md-end">
                <span class="badge bg-light text-dark border p-2 fs-7 me-2">{{ lang('তারিখ:', 'Date:') }} {{ \Carbon\Carbon::parse($date)->format('d F, Y') }}</span>
            </div>
        </form>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="p-3 bg-light rounded border text-center">
                    <small class="text-muted d-block fw-semibold">{{ lang('দিনের প্রারম্ভিক ব্যালেন্স (Opening Balance)', 'Opening Balance') }}</small>
                    <h4 class="fw-bold text-dark mb-0 mt-1">{{ format_currency($totalOpeningBalance) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-success-subtle text-success rounded border border-success text-center">
                    <small class="d-block fw-semibold">{{ lang('আজকের মোট ক্যাশ ইন (+)', 'Total Cash In (+)') }}</small>
                    <h4 class="fw-bold mb-0 mt-1">+ {{ format_currency($totalCashIn) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-danger-subtle text-danger rounded border border-danger text-center">
                    <small class="d-block fw-semibold">{{ lang('আজকের মোট ক্যাশ আউট (-)', 'Total Cash Out (-)') }}</small>
                    <h4 class="fw-bold mb-0 mt-1">- {{ format_currency($totalCashOut) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-emerald-gradient text-white rounded shadow-sm text-center">
                    <small class="text-white-50 d-block fw-semibold">{{ lang('দিনের সমাপনী ব্যালেন্স (Closing Balance)', 'Closing Balance') }}</small>
                    <h4 class="fw-bold mb-0 mt-1">{{ format_currency($closingBalance) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-list text-success me-2"></i>{{ lang('আজকের সকল ক্যাশ ট্রানজেকশন', 'Transactions List') }}</span>
        <button class="btn btn-sm btn-outline-secondary no-print" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট ক্যাশ বুক', 'Print Cash Book') }}</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('সময়/তারিখ', 'Time/Date') }}</th>
                        <th>{{ lang('ক্যাটাগরি', 'Category') }}</th>
                        <th>{{ lang('পেমেন্ট মেথড', 'Method') }}</th>
                        <th>{{ lang('রেফারেন্স', 'Reference') }}</th>
                        <th>{{ lang('ক্যাশ ইন (Cash In ৳)', 'Cash In') }}</th>
                        <th>{{ lang('ক্যাশ আউট (Cash Out ৳)', 'Cash Out') }}</th>
                        <th>{{ lang('বিবরণ', 'Description') }}</th>
                        <th>{{ lang('এন্ট্রি বাই', 'Created By') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($transactions as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $t->created_at->format('h:i A') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $t->category }}</span></td>
                            <td><span class="badge bg-info-subtle text-dark border">{{ $t->paymentMethod->name ?? 'Cash' }}</span></td>
                            <td><code>{{ $t->reference ?? 'N/A' }}</code></td>
                            <td class="text-success fw-bold">{{ $t->type === 'cash_in' ? '+' . format_currency($t->amount) : '-' }}</td>
                            <td class="text-danger fw-bold">{{ $t->type === 'cash_out' ? '-' . format_currency($t->amount) : '-' }}</td>
                            <td>{{ $t->description ?? 'N/A' }}</td>
                            <td>{{ $t->creator->name ?? 'System' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Manual Cash Modal -->
<div class="modal fade" id="createCashModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('cashbook.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('ম্যানুয়াল ক্যাশ বুক এন্ট্রি', 'Manual Cash Entry') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('তারিখ', 'Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ট্রানজেকশনের ধরন', 'Type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="cash_in">{{ lang('ক্যাশ ইন (Cash In +)', 'Cash In (+)') }}</option>
                            <option value="cash_out">{{ lang('ক্যাশ আউট (Cash Out -)', 'Cash Out (-)') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ক্যাটাগরি', 'Category') }} <span class="text-danger">*</span></label>
                        <input type="text" name="category" class="form-control" placeholder="যেমন: Sales, Customer Payment, Investment, Expense" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পরিমাণ (Amount ৳)', 'Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পেমেন্ট মেথড', 'Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method_id" class="form-select" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ format_currency($pm->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('রেফারেন্স', 'Reference') }}</label>
                        <input type="text" name="reference" class="form-control" placeholder="রেফারেন্স নম্বর...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('বিবরণ', 'Description') }}</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="বিবরণ লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">এন্ট্রি সংরক্ষণ করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
