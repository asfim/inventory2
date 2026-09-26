@extends('layouts.app')

@section('title', lang('পেমেন্ট মেথড ব্যবস্থাপনা', 'Payment Methods'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-wallet me-2"></i>{{ lang('পেমেন্ট মেথড ও আলাদা অ্যাকাউন্ট ব্যালেন্স', 'Payment Methods & Ledgers') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('Cash, Bank, bKash, Nagad, Rocket, Upay ইত্যাদির আলাদা হিসাব ও ব্যালেন্স ট্র্যাকিং', 'Dynamic payment methods with individual balance tracking') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createMethodModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন মেথড যুক্ত করুন', 'Add Payment Method') }}
    </button>
</div>

<!-- Summary Cards for Payment Channels -->
<div class="row g-3 mb-4">
    @foreach($methods as $m)
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-0 p-3 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-success">{{ $m->name }}</h6>
                    <span class="badge bg-light text-dark border">{{ $m->cash_transactions_count }} {{ lang('টি ট্রানজেকশন', 'Txns') }}</span>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ format_currency($m->current_balance) }}</h3>
                <small class="text-muted d-block mb-2">Acc: {{ $m->account_number ?? 'N/A' }}</small>
                <a href="{{ route('payment-methods.show', $m->id) }}" class="btn btn-sm btn-outline-success w-100 fw-semibold">
                    <i class="fa-solid fa-receipt me-1"></i> {{ lang('হিসাব দেখুন (Ledger)', 'View Ledger') }}
                </a>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom">
        <i class="fa-solid fa-list text-success me-2"></i> {{ lang('সকল পেমেন্ট মেথডের তালিকা', 'All Payment Methods') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('মেথড নাম', 'Method Name') }}</th>
                        <th>{{ lang('অ্যাকাউন্ট নম্বর', 'Account Number') }}</th>
                        <th>{{ lang('অ্যাকাউন্ট হোল্ডার', 'Account Holder') }}</th>
                        <th>{{ lang('ওপেনিং ব্যালেন্স', 'Opening Balance') }}</th>
                        <th>{{ lang('বর্তমান ব্যালেন্স', 'Current Balance') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($methods as $m)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $m->name }}</td>
                            <td><code>{{ $m->account_number ?? 'N/A' }}</code></td>
                            <td>{{ $m->account_holder ?? 'N/A' }}</td>
                            <td>{{ format_currency($m->opening_balance) }}</td>
                            <td class="fw-bold text-dark fs-6">{{ format_currency($m->current_balance) }}</td>
                            <td>
                                @if($m->status)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('payment-methods.show', $m->id) }}" class="btn btn-sm btn-outline-info" title="Ledger"><i class="fa-solid fa-book me-1"></i> লেজার</a>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMethodModal{{ $m->id }}"><i class="fa-solid fa-pen"></i></button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editMethodModal{{ $m->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('payment-methods.update', $m->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title fw-bold">পেমেন্ট মেথড সম্পাদনা</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">মেথড নাম <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $m->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">অ্যাকাউন্ট নম্বর</label>
                                                <input type="text" name="account_number" class="form-control" value="{{ $m->account_number }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">অ্যাকাউন্ট হোল্ডার</label>
                                                <input type="text" name="account_holder" class="form-control" value="{{ $m->account_holder }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">স্ট্যাটাস</label>
                                                <select name="status" class="form-select">
                                                    <option value="1" {{ $m->status ? 'selected' : '' }}>সক্রিয়</option>
                                                    <option value="0" {{ !$m->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                                            <button type="submit" class="btn btn-success fw-bold">আপডেট করুন</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('payment-methods.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন পেমেন্ট মেথড যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">মেথড নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: Rocket, Upay, Card, Bank B" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">অ্যাকাউন্ট নম্বর</label>
                        <input type="text" name="account_number" class="form-control" placeholder="01700-000000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">অ্যাকাউন্ট হোল্ডার নাম</label>
                        <input type="text" name="account_holder" class="form-control" placeholder="যেমন: দোকান নাম / স্বত্বাধিকারী">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">প্রারম্ভিক ব্যালেন্স (Opening Balance ৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">সংরক্ষণ করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
