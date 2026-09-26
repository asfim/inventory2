@extends('layouts.app')

@section('title', lang('আর্থিক রিপোর্ট ও লাভ-ক্ষতি', 'Financial Profit & Loss Report'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-chart-pie me-2"></i>{{ lang('আর্থিক রিপোর্ট ও লাভ-ক্ষতি হিসাব (Profit & Loss Statement)', 'Financial Profit & Loss') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('নির্দিষ্ট সময়সীমার বিক্রয়, ক্রয়, খরচ, নিট লাভ/ক্ষতি ও ব্যালেন্স হিস্ট্রি', 'Comprehensive financial statement, gross profit, expenses & net profit') }}</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button class="btn btn-outline-secondary fw-semibold" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট স্টেটমেন্ট', 'Print Statement') }}</button>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.financial') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold fs-7">{{ lang('শুরুর তারিখ', 'Start Date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold fs-7">{{ lang('শেষ তারিখ', 'End Date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i> {{ lang('স্টেটমেন্ট রিফ্রেশ', 'Generate Statement') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- P&L Breakdown Card -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-emerald-gradient text-white fw-bold">
                <i class="fa-solid fa-calculator me-1"></i> {{ lang('লাভ-ক্ষতি বিবরণী (Profit & Loss Summary)', 'Profit & Loss Statement') }}
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle mb-0 fs-6">
                    <tbody>
                        <tr>
                            <td><i class="fa-solid fa-cart-shopping text-success me-2"></i>{{ lang('মোট বিক্রয় (Total Sales Revenue):', 'Total Sales Revenue:') }}</td>
                            <td class="text-end fw-bold text-success">{{ format_currency($totalSales) }}</td>
                        </tr>
                        <tr>
                            <td><i class="fa-solid fa-boxes-stacked text-secondary me-2"></i>{{ lang('বিক্রিত পণ্যের ক্রয়মূল্য (Cost of Goods Sold - COGS):', 'Cost of Goods Sold (COGS):') }}</td>
                            <td class="text-end text-danger">- {{ format_currency($cogs) }}</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td><i class="fa-solid fa-chart-line text-primary me-2"></i>{{ lang('মোট গ্রস প্রফিট (Gross Profit):', 'Gross Profit:') }}</td>
                            <td class="text-end text-primary fs-5">{{ format_currency($grossProfit) }}</td>
                        </tr>
                        <tr>
                            <td><i class="fa-solid fa-receipt text-danger me-2"></i>{{ lang('দোকানের মোট পরিচালনা খরচ (Total Operating Expenses):', 'Operating Expenses:') }}</td>
                            <td class="text-end text-danger">- {{ format_currency($totalExpenses) }}</td>
                        </tr>
                        <tr class="table-success fw-bold fs-5">
                            <td><i class="fa-solid fa-sack-dollar text-emerald me-2"></i>{{ lang('নিট লাভ / ক্ষতি (Net Profit / Loss):', 'Net Profit / Loss:') }}</td>
                            <td class="text-end text-success fs-4">{{ format_currency($netProfit) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Receivables & Payables -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ lang('পাওনা ও দেনা সমারি (Dues Summary)', 'Outstanding Dues Summary') }}
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted d-block fs-7 fw-semibold">{{ lang('গ্রাহকের নিকট বাকি পাওনা (Customer Due)', 'Customer Receivables') }}</span>
                            <h3 class="fw-bold text-warning mb-0 mt-1">{{ format_currency($totalCustomerDue) }}</h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted d-block fs-7 fw-semibold">{{ lang('সরবরাহকারীকে প্রদেয় বাকি (Supplier Due)', 'Supplier Payables') }}</span>
                            <h3 class="fw-bold text-danger mb-0 mt-1">{{ format_currency($totalSupplierDue) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Channels Ledger -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 border-bottom">
                <i class="fa-solid fa-wallet text-success me-2"></i>{{ lang('পেমেন্ট মাধ্যম সমূহের বর্তমান ব্যালেন্স', 'Payment Methods Ledgers') }}
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush fs-7">
                    @foreach($paymentMethods as $pm)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <strong class="text-dark d-block fs-6">{{ $pm->name }}</strong>
                                <small class="text-muted">Acc: {{ $pm->account_number ?? 'N/A' }}</small>
                            </div>
                            <span class="fw-bold text-success fs-5">{{ format_currency($pm->current_balance) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
