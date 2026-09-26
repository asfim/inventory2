@extends('layouts.app')

@section('title', lang('মূলধন বিনিয়োগ', 'Investments'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-sack-dollar me-2"></i>{{ lang('মালিক/পার্টনারের মূলধন বিনিয়োগ', 'Owner & Partner Investments') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের কার্যকরী মূলধন বিনিয়োগ ও ক্যাশ/ব্যাংক ব্যালেন্স সমন্বয়', 'Track capital investments & cash injection by partners/owners') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createInvestmentModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন বিনিয়োগ যুক্ত করুন', 'Add Investment') }}
    </button>
</div>

<!-- Investment Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-emerald-gradient text-white">
            <span class="text-white-50 fs-7 fw-semibold">{{ lang('মোট বিনিয়োগ (Total Investment)', 'Total Capital') }}</span>
            <h3 class="fw-bold mb-0 mt-1">{{ format_currency($totalInvestment) }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('ক্যাশ বিনিয়োগ (Cash)', 'Cash Investment') }}</span>
            <h4 class="fw-bold text-success mb-0 mt-1">{{ format_currency($cashInvestment) }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('ব্যাংক বিনিয়োগ (Bank)', 'Bank Investment') }}</span>
            <h4 class="fw-bold text-primary mb-0 mt-1">{{ format_currency($bankInvestment) }}</h4>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('bKash/নগদ বিনিয়োগ (MFS)', 'bKash/Nagad Investment') }}</span>
            <h4 class="fw-bold text-danger mb-0 mt-1">{{ format_currency($bkashInvestment + $nagadInvestment) }}</h4>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom">
        <i class="fa-solid fa-list text-success me-2"></i> {{ lang('বিনিয়োগের ইতিহাস (Investment Records)', 'Investment History Records') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('বিনিয়োগকারীর নাম', 'Investor Name') }}</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('পরিমাণ (Amount)', 'Amount') }}</th>
                        <th>{{ lang('পেমেন্ট মেথড', 'Method') }}</th>
                        <th>{{ lang('রেফারেন্স', 'Reference') }}</th>
                        <th>{{ lang('নোট/বিবরণ', 'Note') }}</th>
                        <th>{{ lang('এন্ট্রি বাই', 'Created By') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($investments as $inv)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $inv->investor_name }}</td>
                            <td>{{ $inv->investment_date->format('d M, Y') }}</td>
                            <td class="fw-bold text-dark fs-6">{{ format_currency($inv->amount) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $inv->paymentMethod->name ?? 'N/A' }}</span></td>
                            <td><code>{{ $inv->reference ?? 'N/A' }}</code></td>
                            <td>{{ $inv->note ?? 'N/A' }}</td>
                            <td>{{ $inv->creator->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">{{ lang('কোন বিনিয়োগ রেকর্ড পাওয়া যায়নি', 'No investment records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Investment Modal -->
<div class="modal fade" id="createInvestmentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('investments.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('নতুন বিনিয়োগ যুক্ত করুন', 'Add New Capital Investment') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('বিনিয়োগকারী/মালিকের নাম', 'Investor Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="investor_name" class="form-control" placeholder="যেমন: হাজী মোঃ নুরুল ইসলাম (মালিক)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('তারিখ', 'Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="investment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('বিনিয়োগের পরিমাণ (Amount ৳)', 'Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold fs-5 text-success" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পেমেন্ট গ্রহণ মাধ্যম', 'Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method_id" class="form-select" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} (বর্তমান ব্যালেন্স: {{ format_currency($pm->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('রেফারেন্স', 'Reference') }}</label>
                        <input type="text" name="reference" class="form-control" placeholder="যেমন: INV-CAP-02">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('নোট / বিবরণ', 'Note') }}</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="বিনিয়োগ সংক্রান্ত নোট..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">বিনিয়োগ কনফার্ম করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
