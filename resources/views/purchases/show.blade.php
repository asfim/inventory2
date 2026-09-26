@extends('layouts.app')

@section('title', 'চালান - ' . $purchase->invoice_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-receipt me-2"></i>{{ lang('পারচেজ ইনভয়েস বিবরণী', 'Purchase Invoice Details') }}</h4>
        <p class="text-muted mb-0 fs-7">Invoice: <code>{{ $purchase->invoice_no }}</code> | Date: {{ $purchase->purchase_date->format('d M, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট চালান', 'Print Invoice') }}
        </a>
        <a href="{{ route('purchases.return', $purchase->id) }}" class="btn btn-warning fw-bold">
            <i class="fa-solid fa-rotate-left me-1"></i> {{ lang('পারচেজ রিটার্ন', 'Purchase Return') }}
        </a>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-emerald-gradient text-white fw-bold">
                <i class="fa-solid fa-list-check me-1"></i> {{ lang('ক্রয়কৃত পণ্যসমূহের তালিকা', 'Purchased Items') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>#</th>
                                <th>{{ lang('পণ্যের নাম', 'Product') }}</th>
                                <th>{{ lang('ব্যাচ নম্বর', 'Batch') }}</th>
                                <th>{{ lang('মেয়াদের তারিখ', 'Expiry') }}</th>
                                <th>{{ lang('পরিমাণ', 'Qty') }}</th>
                                <th>{{ lang('একক মূল্য', 'Rate') }}</th>
                                <th>{{ lang('সাবটোটাল', 'Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @foreach($purchase->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $item->product->name }}</td>
                                    <td><code>{{ $item->batch_number }}</code></td>
                                    <td>{{ $item->expiry_date->format('d M, Y') }}</td>
                                    <td>{{ $item->quantity }} {{ $item->product->unit->short_name ?? '' }}</td>
                                    <td>{{ format_currency($item->unit_price) }}</td>
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
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> {{ lang('হিসাব ও পেমেন্ট সমারি', 'Summary & Payment') }}
            </div>
            <div class="card-body">
                <table class="table table-borderless fs-7 mb-0">
                    <tbody>
                        <tr>
                            <td>{{ lang('সরবরাহকারী:', 'Supplier:') }}</td>
                            <td class="text-end fw-bold">{{ $purchase->supplier->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('পেমেন্ট মেথড:', 'Payment Method:') }}</td>
                            <td class="text-end"><span class="badge bg-light text-dark border">{{ $purchase->paymentMethod->name ?? 'N/A' }}</span></td>
                        </tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr>
                            <td>{{ lang('মোট মূল্য (Gross Total):', 'Gross Total:') }}</td>
                            <td class="text-end fw-bold">{{ format_currency($purchase->total_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('ডিসকাউন্ট ছাড়:', 'Discount:') }}</td>
                            <td class="text-end text-danger">- {{ format_currency($purchase->discount_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('ট্যাক্স/ভ্যাট:', 'Tax/VAT:') }}</td>
                            <td class="text-end">+ {{ format_currency($purchase->tax_amount) }}</td>
                        </tr>
                        <tr class="fs-6 fw-bold border-top border-bottom">
                            <td>{{ lang('নিট প্রদেয় মূল্য:', 'Net Amount:') }}</td>
                            <td class="text-end text-success">{{ format_currency($purchase->net_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('পরিশোধিত:', 'Paid Amount:') }}</td>
                            <td class="text-end text-success fw-bold">{{ format_currency($purchase->paid_amount) }}</td>
                        </tr>
                        <tr>
                            <td>{{ lang('বকেয়া:', 'Due Amount:') }}</td>
                            <td class="text-end text-danger fw-bold">{{ format_currency($purchase->due_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
