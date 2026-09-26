@extends('layouts.app')

@section('title', lang('পণ্য তালিকা', 'Products List'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-flask-vial me-2"></i>{{ lang('কীটনাশক ও কৃষি ঔষধ তালিকা', 'Agro Medicine & Product Inventory') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের সকল কীটনাশক, সার, বীজ ও ওষুধের ক্যাটালগ', 'Manage all agro products, pricing, stock and active ingredients') }}</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-emerald fw-bold shadow-sm">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন পণ্য যুক্ত করুন', 'Add New Product') }}
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('products.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="{{ lang('পণ্য বা সক্রিয় উপাদান দিয়ে খুঁজুন...', 'Search by product or active ingredient...') }}" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- {{ lang('সকল ক্যাটাগরি', 'All Categories') }} --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i> {{ lang('ফিল্টার', 'Filter') }}</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100"><i class="fa-solid fa-rotate me-1"></i> {{ lang('রিসেট', 'Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('পণ্যের নাম ও কোড', 'Product Name & Code') }}</th>
                        <th>{{ lang('সক্রিয় উপাদান (Active Ingredient)', 'Active Ingredient') }}</th>
                        <th>{{ lang('ক্যাটাগরি ও ব্র্যান্ড', 'Category & Brand') }}</th>
                        <th>{{ lang('ক্রয় মূল্য', 'Purchase Price') }}</th>
                        <th>{{ lang('বিক্রয় মূল্য', 'Selling Price') }}</th>
                        <th>{{ lang('বর্তমান স্টক', 'Current Stock') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($products as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('products.show', $p->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $p->name }}
                                </a>
                                <div class="fs-8 text-muted">Code: <code>{{ $p->product_code }}</code> | Barcode: {{ $p->barcode ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $p->active_ingredient ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div>{{ $p->category->name ?? '' }}</div>
                                <small class="text-muted">{{ $p->brand->name ?? '' }}</small>
                            </td>
                            <td class="fw-bold">{{ format_currency($p->purchase_price) }}</td>
                            <td class="fw-bold text-success">{{ format_currency($p->selling_price) }}</td>
                            <td>
                                @if($p->isOutOfStock())
                                    <span class="badge bg-danger">{{ lang('স্টক শেষ', 'Out of Stock') }} (0)</span>
                                @elseif($p->isLowStock())
                                    <span class="badge bg-warning text-dark">{{ lang('স্টক কম', 'Low Stock') }} ({{ $p->current_stock }} {{ $p->unit->short_name }})</span>
                                @else
                                    <span class="badge bg-success">{{ $p->current_stock }} {{ $p->unit->short_name }}</span>
                                @endif
                            </td>
                            <td>
                                @if($p->status)
                                    <span class="badge bg-success-subtle text-success border border-success">{{ lang('সক্রিয়', 'Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ lang('নিষ্ক্রিয়', 'Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', $p->id) }}" class="btn btn-outline-info" title="View Details"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('products.edit', $p->id) }}" class="btn btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('পণ্যটি মুছে ফেলতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
