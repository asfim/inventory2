@extends('layouts.app')

@section('title', $supplier->name . ' - লেজার')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-building-user me-2"></i>{{ $supplier->name }}</h4>
        <p class="text-muted mb-0 fs-7">{{ $supplier->company_name ?? '' }} | {{ $supplier->phone }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paySupplierModal">
            <i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ lang('বকেয়া পরিশোধ করুন', 'Pay Supplier Due') }}
        </button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-rose-gradient text-white p-3">
            <span class="text-white-50 fs-7 fw-semibold">{{ lang('বর্তমান দেনা/বকেয়া (Current Due)', 'Current Outstanding Due') }}</span>
            <h2 class="fw-bold mb-0 mt-1">{{ format_currency($supplier->current_due) }}</h2>
            <small class="text-white-50">{{ lang('সরবরাহকারীকে এই টাকা পরিশোধ করতে হবে', 'Amount payable to supplier') }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('প্রাথমিক বকেয়া', 'Opening Due') }}</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ format_currency($supplier->opening_due) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('মোট পারচেজ চালান সংখ্যা', 'Total Purchases') }}</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $supplier->purchases->count() }} {{ lang('টি চালান', 'Invoices') }}</h3>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-book-journal-whills text-success me-2"></i>{{ lang('সরবরাহকারী লেজার হিস্ট্রি (Supplier Ledger)', 'Supplier Ledger History') }}</span>
        <button class="btn btn-sm btn-outline-secondary no-print" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট', 'Print Ledger') }}</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('ধরন (Type)', 'Type') }}</th>
                        <th>{{ lang('রেফারেন্স', 'Reference') }}</th>
                        <th>{{ lang('পরিশোধ (Debit ৳)', 'Debit (Paid)') }}</th>
                        <th>{{ lang('ক্রয় (Credit ৳)', 'Credit (Purchase)') }}</th>
                        <th>{{ lang('চলতি বাকি (Balance ৳)', 'Balance Due') }}</th>
                        <th>{{ lang('নোট', 'Note') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($supplier->ledgers as $l)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $l->date->format('d M, Y') }}</td>
                            <td>
                                @if($l->type === 'purchase')
                                    <span class="badge bg-info text-dark">{{ lang('ক্রয় (Purchase)', 'Purchase') }}</span>
                                @elseif($l->type === 'payment')
                                    <span class="badge bg-success">{{ lang('পরিশোধ (Payment)', 'Payment') }}</span>
                                @elseif($l->type === 'return')
                                    <span class="badge bg-warning text-dark">{{ lang('রিটার্ন (Return)', 'Return') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $l->type }}</span>
                                @endif
                            </td>
                            <td><code>{{ $l->reference ?? 'N/A' }}</code></td>
                            <td class="text-success fw-bold">{{ $l->debit > 0 ? format_currency($l->debit) : '-' }}</td>
                            <td class="text-danger fw-bold">{{ $l->credit > 0 ? format_currency($l->credit) : '-' }}</td>
                            <td class="fw-bold">{{ format_currency($l->balance) }}</td>
                            <td>{{ $l->note ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">{{ lang('কোন লেজার রেকর্ড পাওয়া যায়নি', 'No ledger records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pay Supplier Modal -->
<div class="modal fade" id="paySupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('suppliers.payment', $supplier->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('সরবরাহকারীকে বকেয়া পরিশোধ', 'Pay Supplier Due') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 bg-light rounded mb-3">
                        <small class="text-muted d-block">{{ lang('সরবরাহকারীর বর্তমান বকেয়া:', 'Current Due:') }}</small>
                        <h4 class="fw-bold text-danger mb-0">{{ format_currency($supplier->current_due) }}</h4>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পরিশোধের পরিমাণ (Amount ৳)', 'Payment Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold text-success fs-5" max="{{ $supplier->current_due }}" placeholder="0.00" required>
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
                        <label class="form-label fw-semibold">{{ lang('নোট / রেফারেন্স', 'Note / Reference') }}</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="পরিশোধের বিবরণ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">পরিশোধ কনফার্ম করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
