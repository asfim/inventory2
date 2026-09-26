@extends('layouts.app')

@section('title', lang('সিস্টেম সেটিং', 'System Settings'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-gear me-2"></i>{{ lang('সিস্টেম ও দোকানের সেটিংস', 'General Shop Settings') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের নাম, ঠিকানা, ফোন নম্বর, মেমো কারেন্সি ও স্টক অ্যালার্ট থ্রেশহোল্ড', 'Configure store identity, phone, address, currency symbol & low stock thresholds') }}</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-emerald-gradient text-white fw-bold">
                <i class="fa-solid fa-sliders me-1"></i> {{ lang('দোকানের সাধারণ সেটিংস', 'Shop Information') }}
            </div>
            <div class="card-body p-4">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ lang('দোকানের নাম (Shop Name)', 'Shop Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" class="form-control fw-bold" value="{{ old('shop_name', $settings['shop_name']) }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">{{ lang('দোকানের ঠিকানা (Address)', 'Address') }}</label>
                            <textarea name="shop_address" class="form-control" rows="2">{{ old('shop_address', $settings['shop_address']) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ lang('ফোন নম্বর (Phone)', 'Phone Number') }}</label>
                            <input type="text" name="shop_phone" class="form-control" value="{{ old('shop_phone', $settings['shop_phone']) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ lang('ইমেইল (Email)', 'Email Address') }}</label>
                            <input type="email" name="shop_email" class="form-control" value="{{ old('shop_email', $settings['shop_email']) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ lang('কারেন্সি সিম্বল (Currency Symbol)', 'Currency Symbol') }} <span class="text-danger">*</span></label>
                            <input type="text" name="currency_symbol" class="form-control fw-bold text-center fs-5" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ lang('লো স্টক অ্যালার্ট থ্রেশহোল্ড (Low Stock Alert)', 'Low Stock Threshold') }} <span class="text-danger">*</span></label>
                            <input type="number" name="low_stock_threshold" class="form-control fw-bold" value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> {{ lang('সেটিংস আপডেট করুন', 'Save Settings') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
