@extends('layouts.app')

@section('title', lang('সেলস রিপোর্ট', 'Sales Report'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-file-invoice-dollar me-2"></i>{{ lang('সেলস ও বিক্রয় রিপোর্ট (Sales Report)', 'Sales Report') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('তারিখ অনুযায়ী বিক্রয়ের তথ্য, গ্রাহকভিত্তিক সেলস ও পেমেন্ট হিস্ট্রি', 'Filter sales report by date range, customer & payment channel') }}</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button class="btn btn-outline-secondary fw-semibold" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট রিপোর্ট', 'Print Report') }}</button>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('reports.sales') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-7">{{ lang('শুরুর তারিখ', 'Start Date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-7">{{ lang('শেষ তারিখ', 'End Date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-7">{{ lang('গ্রাহক নির্বাচন', 'Select Customer') }}</label>
                <select name="customer_id" class="form-select">
                    <option value="">-- সকল গ্রাহক --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i> {{ lang('রিপোর্ট দেখুন', 'View Report') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-3 bg-emerald-gradient text-white">
            <span class="text-white-50 fs-7 fw-semibold">{{ lang('মোট বিক্রয় মূল্য (Total Sales)', 'Total Sales Amount') }}</span>
            <h3 class="fw-bold mb-0 mt-1">{{ format_currency($totalSalesAmount) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('মোট নগদ আদায় (Total Paid)', 'Total Paid Amount') }}</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ format_currency($totalPaidAmount) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('মোট বাকি (Total Customer Due)', 'Total Due Amount') }}</span>
            <h3 class="fw-bold text-danger mb-0 mt-1">{{ format_currency($totalDueAmount) }}</h3>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('ইনভয়েস', 'Invoice') }}</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('গ্রাহক', 'Customer') }}</th>
                        <th>{{ lang('পেমেন্ট মাধ্যম', 'Method') }}</th>
                        <th>{{ lang('সর্বমোট', 'Total') }}</th>
                        <th>{{ lang('পরিশোধ', 'Paid') }}</th>
                        <th>{{ lang('বকেয়া', 'Due') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($sales as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><code>{{ $s->invoice_no }}</code></td>
                            <td>{{ $s->sale_date->format('d M, Y') }}</td>
                            <td class="fw-bold text-success">{{ $s->customer->name }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $s->paymentMethod->name ?? 'Cash' }}</span></td>
                            <td class="fw-bold">{{ format_currency($s->net_amount) }}</td>
                            <td class="text-success">{{ format_currency($s->paid_amount) }}</td>
                            <td class="text-danger fw-bold">{{ format_currency($s->due_amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
