@extends('layouts.app')

@section('title', lang('স্টক রিপোর্ট', 'Stock Report'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-boxes-packing me-2"></i>{{ lang('স্টক ও ভ্যালুয়েশন রিপোর্ট (Stock Valuation Report)', 'Stock Report') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের সমস্ত ওষুধের বর্তমান স্টক, ক্রয়মূল্য ভ্যালু ও সম্ভাব্য বিক্রয়মূল্য ভ্যালু', 'Total inventory stock value at purchase price & expected selling price') }}</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button class="btn btn-outline-secondary fw-semibold" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট রিপোর্ট', 'Print Report') }}</button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-emerald-gradient text-white">
            <span class="text-white-50 fs-7 fw-semibold">{{ lang('মোট মজুদ স্টকের ক্রয়মূল্য (Stock Valuation at Purchase Price)', 'Inventory Value (Purchase)') }}</span>
            <h3 class="fw-bold mb-0 mt-1">{{ format_currency($totalStockValue) }}</h3>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('মোট মজুদ স্টকের সম্ভাব্য বিক্রয়মূল্য (Stock Valuation at Selling Price)', 'Inventory Value (Selling)') }}</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ format_currency($totalSellingValue) }}</h3>
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
                        <th>{{ lang('পণ্যের নাম', 'Product') }}</th>
                        <th>{{ lang('কোড/SKU', 'Code/SKU') }}</th>
                        <th>{{ lang('ক্যাটাগরি', 'Category') }}</th>
                        <th>{{ lang('বর্তমান মজুদ', 'Stock') }}</th>
                        <th>{{ lang('ক্রয় মূল্য (৳)', 'Purchase Rate') }}</th>
                        <th>{{ lang('মোট ক্রয়ভ্যালু (৳)', 'Stock Value') }}</th>
                        <th>{{ lang('বিক্রয় মূল্য (৳)', 'Selling Rate') }}</th>
                        <th>{{ lang('মোট বিক্রয়ভ্যালু (৳)', 'Expected Sale Value') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($products as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold text-success">{{ $p->name }}</td>
                            <td><code>{{ $p->product_code }}</code></td>
                            <td>{{ $p->category->name ?? '' }}</td>
                            <td class="fw-bold">{{ $p->current_stock }} {{ $p->unit->short_name ?? '' }}</td>
                            <td>{{ format_currency($p->purchase_price) }}</td>
                            <td class="fw-bold">{{ format_currency($p->current_stock * $p->purchase_price) }}</td>
                            <td>{{ format_currency($p->selling_price) }}</td>
                            <td class="fw-bold text-success">{{ format_currency($p->current_stock * $p->selling_price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
