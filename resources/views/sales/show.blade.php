@extends('layouts.app')

@section('title', 'বিক্রয় মেমো - ' . $sale->invoice_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-receipt me-2"></i>{{ lang('বিক্রয় মেমো বিবরণী', 'Sales Invoice Details') }}</h4>
        <p class="text-muted mb-0 fs-7">Invoice: <code>{{ $sale->invoice_no }}</code> | Date: {{ $sale->sale_date->format('d M, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1"></i> {{ lang('মেমো প্রিন্ট করুন', 'Print Invoice') }}
        </a>
        <a href="{{ route('sales.return', $sale->id) }}" class="btn btn-warning fw-bold">
            <i class="fa-solid fa-rotate-left me-1"></i> {{ lang('সেলস রিটার্ন', 'Sales Return') }}
        </a>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-emerald-gradient text-white fw-bold">
                <i class="fa-solid fa-list-check me-1"></i> {{ lang('বিক্রীত পণ্যসমূহের তালিকা', 'Sold Items') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>#</th>
                                <th>{{ lang('পণ্যের নাম', 'Product Name') }}</th>
                                <th>{{ lang('ব্যাচ নম্বর', 'Batch No') }}</th>
                                <th>{{ lang('পরিমাণ', 'Quantity') }}</th>
                                <th>{{ lang('একক দর', 'Rate') }}</th>
                                <th>{{ lang('ডিসকাউন্ট', 'Discount') }}</th>
                                <th>{{ lang('সাবটোটাল', 'Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $item->product->name }}</td>
                                    <td><code>{{ $item->batch_number ?? 'DEFAULT' }}</code></td>
                                    <td>{{ $item->quantity }} {{ $item->product->unit->short_name ?? '' }}</td>
                                    <td>{{ format_currency($item->unit_price) }}</td>
                                    <td>{{ format_currency($item->discount) }}</td>
                                    <td class="fw-bold">{{ format_currency($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="fa-solid fa-calculator me-1"></i> {{ lang('হিসাব ও মেমো সমারি', 'Memo Summary') }}
            </div>
            <div class="card-body">
                <table class="table table-borderless fs-7 mb-0">
                    <tbody>
                        <tr>
                            <td>{{ lang('গ্রাহক (Customer):', 'Customer:') }}</td>
                            <td class="text-end fw-bold">{{ $sale->customer->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('বিক্রেতা (Seller):', 'Seller:') }}</td>
                            <td class="text-end">{{ $sale->seller->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('পেমেন্ট মাধ্যম:', 'Payment Method:') }}</td>
                            <td class="text-end"><span class="badge bg-light text-dark border">{{ $sale->paymentMethod->name ?? 'Cash' }}</span></td>
                        </tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr>
                            <td>{{ lang('মোট মূল্য (Gross Total):', 'Gross Total:') }}</td>
                            <td class="text-end fw-bold">{{ format_currency($sale->total_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('ডিসকাউন্ট ছাড়:', 'Discount:') }}</td>
                            <td class="text-end text-danger">- {{ format_currency($sale->discount_amount) }}</td>
                        </tr>
                        <tr class="fs-6 fw-bold border-top border-bottom">
                            <td>{{ lang('সর্বমোট (Net Amount):', 'Net Amount:') }}</td>
                            <td class="text-end text-success">{{ format_currency($sale->net_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('নগদ প্রাপ্তি:', 'Paid Amount:') }}</td>
                            <td class="text-end text-success fw-bold">{{ format_currency($sale->paid_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('বকেয়া (Due):', 'Due Amount:') }}</td>
                            <td class="text-end text-danger fw-bold">{{ format_currency($sale->due_amount) }}</td>
                        </tr>
                        @if($sale->change_amount > 0)
                        <tr>
                            <td>{{ lang('ফেরত (Change):', 'Change:') }}</td>
                            <td class="text-end text-primary fw-bold">{{ format_currency($sale->change_amount) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
