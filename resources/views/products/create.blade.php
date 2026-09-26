@extends('layouts.app')

@section('title', lang('নতুন পণ্য যুক্ত করুন', 'Add New Product'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-plus-circle me-2"></i>{{ lang('নতুন কীটনাশক / পণ্য এন্ট্রি', 'Add New Agro Product') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('পণ্য তৈরি ও প্রারম্ভিক ব্যাচ ট্র্যাকিং তথ্য দিন', 'Enter product information, active ingredients and initial batch stock') }}</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
    </a>
</div>

<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Main Product Information -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-emerald-gradient text-white fw-bold">
                    <i class="fa-solid fa-circle-info me-1"></i> {{ lang('প্রাথমিক বিবরণ', 'Product General Details') }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">{{ lang('পণ্যের নাম (Product Name)', 'Product Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="যেমন: ভার্টিমেক ১৮ ইসি (Vertimec 18 EC)" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('পণ্য কোড (Product Code)', 'Product Code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="product_code" class="form-control" value="{{ old('product_code', 'P-' . strtoupper(Str::random(6))) }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ lang('সক্রিয় উপাদান / জেনারেটিক নাম (Active Ingredient)', 'Active Ingredient') }}</label>
                            <input type="text" name="active_ingredient" class="form-control" placeholder="যেমন: Mancozeb 80% WP, Abamectin 1.8% EC, Glyphosate 48% SL" value="{{ old('active_ingredient') }}">
                            <small class="text-muted">{{ lang('কৃষকদের জন্য ওষুধের মূল উপাদান লিখে রাখা অত্যন্ত সহায়ক', 'Helps farmers identify active chemical ingredient') }}</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('ক্যাটাগরি', 'Category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('কোম্পানি / ব্র্যান্ড', 'Company / Brand') }}</label>
                            <select name="brand_id" class="form-select">
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('পরিমাপের ইউনিট', 'Unit') }} <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select" required>
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->short_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SKU (Stock Keeping Unit)</label>
                            <input type="text" name="sku" class="form-control" placeholder="যেমন: VERT-100" value="{{ old('sku') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ lang('বারকোড (Barcode)', 'Barcode') }}</label>
                            <input type="text" name="barcode" class="form-control" placeholder="বারকোড স্ক্যানার দিয়ে স্ক্যান করুন" value="{{ old('barcode') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ lang('বিবরণ / ব্যবহারবিধি', 'Description / Usage Instructions') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="পোকা দমনের নিয়ম ও প্রয়োগের তথ্য...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Stock Details -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fa-solid fa-tag me-1"></i> {{ lang('মূল্য ও স্টক পরিমাণ', 'Pricing & Stock Thresholds') }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ক্রয় মূল্য (Purchase Price ৳)', 'Purchase Price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control fw-bold" placeholder="0.00" value="{{ old('purchase_price') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('খুচরা বিক্রয় মূল্য (Selling Price ৳)', 'Selling Price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="selling_price" class="form-control fw-bold text-success" placeholder="0.00" value="{{ old('selling_price') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পাইকারি বিক্রয় মূল্য (Wholesale Price ৳)', 'Wholesale Price') }}</label>
                        <input type="number" step="0.01" name="wholesale_price" class="form-control" placeholder="0.00" value="{{ old('wholesale_price') }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('সর্বনিম্ন স্টক', 'Min Stock') }} <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 5) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('সর্বোচ্চ স্টক', 'Max Stock') }}</label>
                            <input type="number" name="max_stock" class="form-control" value="{{ old('max_stock', 500) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Initial Stock & Batch Details -->
            <div class="card shadow-sm border-0 mb-4 border-warning">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fa-solid fa-calendar-days me-1"></i> {{ lang('প্রারম্ভিক স্টক ও ব্যাচ', 'Opening Stock & Batch') }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('প্রারম্ভিক স্টক (Opening Stock)', 'Opening Quantity') }}</label>
                        <input type="number" name="current_stock" class="form-control" value="{{ old('current_stock', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ব্যাচ নম্বর (Batch No)', 'Batch Number') }}</label>
                        <input type="text" name="batch_number" class="form-control" placeholder="যেমন: SYN-2026-A1" value="{{ old('batch_number') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('মেয়াদের তারিখ (Expiry Date)', 'Expiry Date') }}</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                <i class="fa-solid fa-check-circle me-1"></i> {{ lang('পণ্য সংরক্ষণ করুন', 'Save Product') }}
            </button>
        </div>
    </div>
</form>
@endsection
