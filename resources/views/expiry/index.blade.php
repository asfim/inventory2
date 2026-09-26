@extends('layouts.app')

@section('title', lang('মেয়াদ ব্যবস্থাপনা (Expiry Management)', 'Expiry Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-danger"><i class="fa-solid fa-calendar-xmark me-2"></i>{{ lang('কীটনাশক ও ঔষধের মেয়াদ ব্যবস্থাপনা (Expiry Management)', 'Expiry Management') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('কীটনাশক/কৃষি ঔষধের মেয়াদের তারিখ মনিটরিং ও পূর্বাহ্নেই অ্যালার্ট ট্র্যাকিং', 'Monitor agro medicine expiry dates & track items expiring within 30, 60 & 90 days') }}</p>
    </div>
</div>

<!-- Summary Cards for Expiry Status -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-danger text-white">
            <span class="text-white-50 fs-7 fw-semibold">{{ lang('মেয়াদ উত্তীর্ণ পণ্য (Expired Stock)', 'Expired Stock') }}</span>
            <h3 class="fw-bold mb-0 mt-1">{{ $expiredCount }} {{ lang('টি আইটেম', 'Items') }}</h3>
            <small class="text-white-50">{{ lang('ঝুঁকিপূর্ণ মূল্য:', 'At Risk:') }} {{ format_currency($expiredValue) }}</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-warning text-dark">
            <span class="text-dark-50 fs-7 fw-semibold">{{ lang('৩০ দিনের মধ্যে মেয়াদ শেষ', 'Expiring in 30 Days') }}</span>
            <h3 class="fw-bold mb-0 mt-1">{{ $near30Count }} {{ lang('টি আইটেম', 'Items') }}</h3>
            <small class="text-dark-50">{{ lang('জরুরী বিক্রয় প্রয়োজন', 'Urgent Action Needed') }}</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('৬০ দিনের মধ্যে মেয়াদ শেষ', 'Expiring in 60 Days') }}</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $near60Count }} {{ lang('টি আইটেম', 'Items') }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('৯০ দিনের মধ্যে মেয়াদ শেষ', 'Expiring in 90 Days') }}</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $near90Count }} {{ lang('টি আইটেম', 'Items') }}</h3>
        </div>
    </div>
</div>

<!-- Expiry Filters Buttons -->
<div class="mb-4">
    <div class="btn-group w-100 shadow-sm">
        <a href="{{ route('expiry.index', ['filter' => 'all']) }}" class="btn btn-outline-secondary {{ $filter === 'all' ? 'active' : '' }}">
            {{ lang('সকল ব্যাচ (All Batches)', 'All Batches') }}
        </a>
        <a href="{{ route('expiry.index', ['filter' => 'expired']) }}" class="btn btn-outline-danger {{ $filter === 'expired' ? 'active' : '' }}">
            <i class="fa-solid fa-skull-crossbones me-1"></i> {{ lang('মেয়াদ উত্তীর্ণ (Expired)', 'Expired Only') }}
        </a>
        <a href="{{ route('expiry.index', ['filter' => '30_days']) }}" class="btn btn-outline-warning {{ $filter === '30_days' ? 'active' : '' }}">
            <i class="fa-solid fa-clock me-1"></i> {{ lang('৩০ দিনের মধ্যে (Within 30 Days)', 'Within 30 Days') }}
        </a>
        <a href="{{ route('expiry.index', ['filter' => '60_days']) }}" class="btn btn-outline-info {{ $filter === '60_days' ? 'active' : '' }}">
            {{ lang('৬০ দিনের মধ্যে (Within 60 Days)', 'Within 60 Days') }}
        </a>
        <a href="{{ route('expiry.index', ['filter' => '90_days']) }}" class="btn btn-outline-primary {{ $filter === '90_days' ? 'active' : '' }}">
            {{ lang('৯০ দিনের মধ্যে (Within 90 Days)', 'Within 90 Days') }}
        </a>
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
                        <th>{{ lang('ক্যাটাগরি', 'Category') }}</th>
                        <th>{{ lang('ব্যাচ নম্বর (Batch)', 'Batch No') }}</th>
                        <th>{{ lang('মেয়াদের তারিখ (Expiry Date)', 'Expiry Date') }}</th>
                        <th>{{ lang('বর্তমান স্টক', 'Current Stock') }}</th>
                        <th>{{ lang('ক্রয় মূল্য', 'Purchase Price') }}</th>
                        <th>{{ lang('মোট স্টক ভ্যালু', 'Stock Value') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Expiry Status') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($batches as $b)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('products.show', $b->product->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $b->product->name }}
                                </a>
                            </td>
                            <td>{{ $b->product->category->name ?? '' }}</td>
                            <td><code>{{ $b->batch_number }}</code></td>
                            <td class="fw-bold">{{ $b->expiry_date->format('d M, Y') }}</td>
                            <td>{{ $b->quantity }} {{ $b->product->unit->short_name ?? '' }}</td>
                            <td>{{ format_currency($b->purchase_price) }}</td>
                            <td class="fw-bold">{{ format_currency($b->quantity * $b->purchase_price) }}</td>
                            <td>
                                @if($b->days_until_expiry < 0)
                                    <span class="badge badge-expiry-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ lang('মেয়াদ উত্তীর্ণ', 'Expired') }} ({{ abs($b->days_until_expiry) }} {{ lang('দিন পূর্বে', 'days ago') }})</span>
                                @elseif($b->days_until_expiry <= 30)
                                    <span class="badge badge-expiry-warning"><i class="fa-solid fa-clock me-1"></i>{{ $b->days_until_expiry }} {{ lang('দিন বাকি', 'days left') }}</span>
                                @elseif($b->days_until_expiry <= 60)
                                    <span class="badge bg-info text-dark">{{ $b->days_until_expiry }} {{ lang('দিন বাকি', 'days left') }}</span>
                                @else
                                    <span class="badge bg-success">{{ lang('সঠিক', 'Valid') }} ({{ $b->days_until_expiry }} {{ lang('দিন বাকি', 'days left') }})</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">{{ lang('কোন তথ্য পাওয়া যায়নি', 'No batch records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
