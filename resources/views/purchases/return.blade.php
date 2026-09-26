@extends('layouts.app')

@section('title', 'পারচেজ রিটার্ন - ' . $purchase->invoice_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-warning"><i class="fa-solid fa-rotate-left me-2"></i>{{ lang('পারচেজ রিটার্ন এন্ট্রি', 'Purchase Return Entry') }}</h4>
        <p class="text-muted mb-0 fs-7">Invoice: <code>{{ $purchase->invoice_no }}</code> | Supplier: {{ $purchase->supplier->name }}</p>
    </div>
    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('ফিরে যান', 'Back') }}
    </a>
</div>

<form action="{{ route('purchases.return.store', $purchase->id) }}" method="POST">
    @csrf
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fa-solid fa-boxes-stacked me-1"></i> {{ lang('ফেরতযোগ্য পণ্য ও পরিমাণ নির্বাচন', 'Return Items & Quantity') }}
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light fs-7">
                        <tr>
                            <th>#</th>
                            <th>{{ lang('পণ্যের নাম', 'Product') }}</th>
                            <th>{{ lang('ব্যাচ নম্বর', 'Batch') }}</th>
                            <th>{{ lang('ক্রয়কৃত পরিমাণ', 'Purchased Qty') }}</th>
                            <th>{{ lang('একক দর', 'Rate') }}</th>
                            <th style="width: 20%;">{{ lang('ফেরতের পরিমাণ (Return Qty)', 'Return Qty') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7">
                        @foreach($purchase->items as $i => $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong class="text-success">{{ $item->product->name }}</strong>
                                    <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                                </td>
                                <td>
                                    <code>{{ $item->batch_number }}</code>
                                    <input type="hidden" name="items[{{ $i }}][batch_number]" value="{{ $item->batch_number }}">
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    {{ format_currency($item->unit_price) }}
                                    <input type="hidden" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control fw-bold text-danger" value="0" min="0" max="{{ $item->quantity }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ lang('রিটার্নের তারিখ', 'Return Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ lang('ফেরত টাকা গ্রহণের মাধ্যম', 'Payment Method') }} <span class="text-danger">*</span></label>
                    <select name="payment_method_id" class="form-select" required>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ lang('রিটার্নের কারণ (Reason)', 'Reason') }}</label>
                    <input type="text" name="reason" class="form-control" placeholder="যেমন: ড্যামেজ / মেয়াদোত্তীর্ণ পণ্য ফেরত">
                </div>
            </div>

            <button type="submit" class="btn btn-warning btn-lg fw-bold w-100 mt-4 shadow-sm" onclick="return confirm('পারচেজ রিটার্ন নিশ্চিত করছেন? স্টক হ্রাস পাবে!');">
                <i class="fa-solid fa-rotate-left me-1"></i> {{ lang('পারচেজ রিটার্ন সম্পাদন করুন', 'Submit Purchase Return') }}
            </button>
        </div>
    </div>
</form>
@endsection
