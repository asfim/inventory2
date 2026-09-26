@extends('layouts.app')

@section('title', lang('পণ্য সম্পাদনা', 'Edit Product'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-pen-to-square me-2"></i>{{ lang('পণ্য তথ্য পরিবর্তন', 'Edit Agro Product') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ $product->name }} ({{ $product->product_code }})</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
    </a>
</div>

<form action="{{ route('products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-emerald-gradient text-white fw-bold">
                    <i class="fa-solid fa-circle-info me-1"></i> {{ lang('বিবরণ সম্পাদন', 'Edit Details') }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">{{ lang('পণ্যের নাম (Product Name)', 'Product Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('পণ্য কোড (Product Code)', 'Product Code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="product_code" class="form-control" value="{{ old('product_code', $product->product_code) }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ lang('সক্রিয় উপাদান (Active Ingredient)', 'Active Ingredient') }}</label>
                            <input type="text" name="active_ingredient" class="form-control" value="{{ old('active_ingredient', $product->active_ingredient) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('ক্যাটাগরি', 'Category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('কোম্পানি / ব্র্যান্ড', 'Company / Brand') }}</label>
                            <select name="brand_id" class="form-select">
                                <option value="">-- নির্বাচন করুন --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" {{ old('brand_id', $product->brand_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('ইউনিট', 'Unit') }} <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select" required>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ lang('বারকোড (Barcode)', 'Barcode') }}</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ lang('বিবরণ', 'Description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fa-solid fa-tag me-1"></i> {{ lang('মূল্য ও স্ট্যাটাস', 'Pricing & Status') }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('ক্রয় মূল্য (৳)', 'Purchase Price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control fw-bold" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('বিক্রয় মূল্য (৳)', 'Selling Price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="selling_price" class="form-control fw-bold text-success" value="{{ old('selling_price', $product->selling_price) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পাইকারি মূল্য (৳)', 'Wholesale Price') }}</label>
                        <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $product->wholesale_price) }}">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('সর্বনিম্ন স্টক', 'Min Stock') }} <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('সর্বোচ্চ স্টক', 'Max Stock') }}</label>
                            <input type="number" name="max_stock" class="form-control" value="{{ old('max_stock', $product->max_stock) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ lang('স্ট্যাটাস', 'Status') }}</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $product->status ? 'selected' : '' }}>{{ lang('সক্রিয় (Active)', 'Active') }}</option>
                            <option value="0" {{ !$product->status ? 'selected' : '' }}>{{ lang('নিষ্ক্রিয় (Inactive)', 'Inactive') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> {{ lang('আপডেট সংরক্ষণ করুন', 'Update Product') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
