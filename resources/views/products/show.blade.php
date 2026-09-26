@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-flask-vial me-2"></i>{{ $product->name }}</h4>
        <p class="text-muted mb-0 fs-7">Code: <code>{{ $product->product_code }}</code> | Barcode: {{ $product->barcode ?? 'N/A' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-pen me-1"></i> {{ lang('সম্পাদনা', 'Edit Product') }}
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Main Information -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-emerald-gradient text-white fw-bold">
                <i class="fa-solid fa-circle-info me-1"></i> {{ lang('পণ্যের বিস্তারিত ও সক্রিয় উপাদান', 'Product Information') }}
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th class="bg-light" style="width: 30%;">{{ lang('পণ্যের নাম', 'Product Name') }}</th>
                            <td class="fw-bold text-success fs-6">{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ lang('সক্রিয় উপাদান (Active Ingredient)', 'Active Ingredient') }}</th>
                            <td><span class="badge bg-success-subtle text-success border fs-7">{{ $product->active_ingredient ?? 'N/A' }}</span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ lang('ক্যাটাগরি', 'Category') }}</th>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ lang('কোম্পানি/ব্র্যান্ড (Manufacturer)', 'Manufacturer/Brand') }}</th>
                            <td>{{ $product->brand->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ lang('পরিমাপের ইউনিট', 'Unit') }}</th>
                            <td>{{ $product->unit->name ?? 'N/A' }} ({{ $product->unit->short_name ?? '' }})</td>
                        </tr>
                        <tr>
                            <th class="bg-light">SKU</th>
                            <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ lang('বারকোড', 'Barcode') }}</th>
                            <td>{{ $product->barcode ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">{{ lang('বিবরণ / নির্দেশনা', 'Description') }}</th>
                            <td>{{ $product->description ?? 'কোন বিবরণ প্রদান করা হয়নি।' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Batches & Expiry Dates -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold border-bottom d-flex justify-content-between align-items-center py-3">
                <span><i class="fa-solid fa-boxes-stacked text-warning me-1"></i> {{ lang('স্টক ব্যাচ ও মেয়াদের তালিকা (Product Batches & Expiry)', 'Batches & Expiry Dates') }}</span>
                <span class="badge bg-dark">{{ $product->batches->count() }} {{ lang('টি ব্যাচ', 'Batches') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>#</th>
                                <th>{{ lang('ব্যাচ নম্বর', 'Batch Number') }}</th>
                                <th>{{ lang('মেয়াদের তারিখ', 'Expiry Date') }}</th>
                                <th>{{ lang('পরিমাণ', 'Quantity') }}</th>
                                <th>{{ lang('ক্রয় মূল্য', 'Purchase Price') }}</th>
                                <th>{{ lang('বিক্রয় মূল্য', 'Selling Price') }}</th>
                                <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @forelse($product->batches as $b)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $b->batch_number }}</code></td>
                                    <td>{{ $b->expiry_date->format('d M, Y') }}</td>
                                    <td class="fw-bold">{{ $b->quantity }} {{ $product->unit->short_name }}</td>
                                    <td>{{ format_currency($b->purchase_price) }}</td>
                                    <td>{{ format_currency($b->selling_price) }}</td>
                                    <td>
                                        @if($b->days_until_expiry < 0)
                                            <span class="badge bg-danger">{{ lang('মেয়াদ উত্তীর্ণ', 'Expired') }}</span>
                                        @elseif($b->days_until_expiry <= 60)
                                            <span class="badge bg-warning text-dark">{{ $b->days_until_expiry }} {{ lang('দিন বাকি', 'days left') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ lang('সঠিক', 'Valid') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted">{{ lang('কোন ব্যাচ রেকর্ড পাওয়া যায়নি', 'No batches found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Summary Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="fa-solid fa-calculator me-1"></i> {{ lang('মূল্য ও স্টক সমারি', 'Pricing & Stock Summary') }}
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded mb-3 text-center">
                    <small class="text-muted text-uppercase d-block fw-semibold">{{ lang('বর্তমান মজুদ স্টক', 'Current Stock') }}</small>
                    <h2 class="fw-bold text-success mb-0 mt-1">{{ $product->current_stock }} {{ $product->unit->short_name }}</h2>
                    <small class="text-muted">{{ lang('সর্বনিম্ন সীমা:', 'Min Threshold:') }} {{ $product->min_stock }} {{ $product->unit->short_name }}</small>
                </div>

                <div class="p-3 bg-light rounded mb-3 text-center">
                    <small class="text-muted text-uppercase d-block fw-semibold">{{ lang('মোট মজুদ স্টকের ক্রয়মূল্য (Stock Value)', 'Total Stock Value') }}</small>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ format_currency($product->stock_value) }}</h3>
                </div>

                <ul class="list-group list-group-flush mb-3 fs-7">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ lang('একক ক্রয় মূল্য', 'Purchase Price') }}:</span>
                        <strong class="text-dark">{{ format_currency($product->purchase_price) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ lang('খুচরা বিক্রয় মূল্য', 'Selling Price') }}:</span>
                        <strong class="text-success">{{ format_currency($product->selling_price) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ lang('পাইকারি মূল্য', 'Wholesale Price') }}:</span>
                        <strong>{{ format_currency($product->wholesale_price) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ lang('একক প্রফিট মার্জিন', 'Profit Margin') }}:</span>
                        <strong class="text-primary">{{ format_currency($product->selling_price - $product->purchase_price) }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
