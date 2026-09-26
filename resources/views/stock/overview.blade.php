@extends('layouts.app')

@section('title', lang('স্টক ওভারভিউ', 'Stock Overview'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-boxes-stacked me-2"></i>{{ lang('কম্প্রিহেনসিভ স্টক ওভারভিউ', 'Stock Overview') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের বর্তমান মজুদ, স্টক ভ্যালু, ব্যাচ নম্বর ও মেয়াদের তথ্যাবলী', 'Live stock levels, inventory valuation & stock status') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.adjustments') }}" class="btn btn-outline-warning fw-bold">
            <i class="fa-solid fa-sliders me-1"></i> {{ lang('স্টক এডজাস্টমেন্ট', 'Stock Adjustment') }}
        </a>
        <a href="{{ route('stock.transfers') }}" class="btn btn-outline-info fw-bold">
            <i class="fa-solid fa-right-left me-1"></i> {{ lang('স্টক ট্রান্সফার', 'Stock Transfer') }}
        </a>
    </div>
</div>

<!-- Stock Status Filter Tabs & Summary Card -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="btn-group w-100 shadow-sm">
            <a href="{{ route('stock.overview', ['status' => 'all']) }}" class="btn btn-outline-success {{ $status === 'all' ? 'active' : '' }}">
                <i class="fa-solid fa-list me-1"></i> {{ lang('সকল স্টক (All)', 'All Products') }}
            </a>
            <a href="{{ route('stock.overview', ['status' => 'in']) }}" class="btn btn-outline-success {{ $status === 'in' ? 'active' : '' }}">
                <i class="fa-solid fa-circle-check me-1"></i> {{ lang('পর্যাপ্ত স্টক (In Stock)', 'In Stock') }}
            </a>
            <a href="{{ route('stock.overview', ['status' => 'low']) }}" class="btn btn-outline-warning {{ $status === 'low' ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ lang('কম স্টক (Low Stock)', 'Low Stock') }}
            </a>
            <a href="{{ route('stock.overview', ['status' => 'out']) }}" class="btn btn-outline-danger {{ $status === 'out' ? 'active' : '' }}">
                <i class="fa-solid fa-ban me-1"></i> {{ lang('স্টক শেষ (Out of Stock)', 'Out of Stock') }}
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 bg-emerald-gradient text-white rounded shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <small class="text-white-50 uppercase fw-semibold d-block">{{ lang('মোট মজুদ স্টকের ক্রয়মূল্য (Stock Value)', 'Total Inventory Valuation') }}</small>
                <h3 class="fw-bold mb-0 mt-1">{{ format_currency($totalStockValue) }}</h3>
            </div>
            <i class="fa-solid fa-sack-dollar fs-1 text-white-50"></i>
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
                        <th>{{ lang('পণ্য ও কোড', 'Product') }}</th>
                        <th>SKU</th>
                        <th>{{ lang('ক্যাটাগরি', 'Category') }}</th>
                        <th>{{ lang('ক্রয় মূল্য', 'Purchase Price') }}</th>
                        <th>{{ lang('বিক্রয় মূল্য', 'Selling Price') }}</th>
                        <th>{{ lang('বর্তমান স্টক', 'Current Stock') }}</th>
                        <th>{{ lang('স্টক ভ্যালু (৳)', 'Stock Value') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($products as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('products.show', $p->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $p->name }}
                                </a>
                                <div class="fs-8 text-muted">Code: <code>{{ $p->product_code }}</code></div>
                            </td>
                            <td><code>{{ $p->sku ?? 'N/A' }}</code></td>
                            <td>{{ $p->category->name ?? '' }}</td>
                            <td>{{ format_currency($p->purchase_price) }}</td>
                            <td>{{ format_currency($p->selling_price) }}</td>
                            <td class="fw-bold fs-6">{{ $p->current_stock }} {{ $p->unit->short_name ?? '' }}</td>
                            <td class="fw-bold text-dark">{{ format_currency($p->stock_value) }}</td>
                            <td>
                                @if($p->isOutOfStock())
                                    <span class="badge badge-stock-out">{{ lang('স্টক শেষ', 'Out of Stock') }}</span>
                                @elseif($p->isLowStock())
                                    <span class="badge badge-stock-low">{{ lang('স্টক কম (Alert)', 'Low Stock') }}</span>
                                @else
                                    <span class="badge badge-stock-in">{{ lang('ইন স্টক', 'In Stock') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">{{ lang('কোন তথ্য পাওয়া যায়নি', 'No stock records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
