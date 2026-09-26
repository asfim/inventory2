@extends('layouts.app')

@section('title', lang('স্টক এডজাস্টমেন্ট', 'Stock Adjustments'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-sliders me-2"></i>{{ lang('স্টক এডজাস্টমেন্ট (Stock Adjustment)', 'Stock Adjustments') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('ড্যামেজ, মেয়াদের তারিখ শেষ, হারানো বা উদ্বোধনী স্টক ম্যানুয়ালি বৃদ্ধি/হ্রাস করুন', 'Manually adjust stock due to damage, expiry, loss or opening stock correction') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createAdjustmentModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন এডজাস্টমেন্ট এন্ট্রি', 'New Adjustment') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom">
        <i class="fa-solid fa-clock-rotate-left me-1"></i> {{ lang('স্টক এডজাস্টমেন্ট হিস্ট্রি (Adjustment History Log)', 'Adjustment History Log') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('এডজাস্টমেন্ট নং', 'Adjustment No') }}</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('ধরন (Type)', 'Type') }}</th>
                        <th>{{ lang('কারণ (Reason)', 'Reason') }}</th>
                        <th>{{ lang('আইটেমস', 'Adjusted Items') }}</th>
                        <th>{{ lang('এন্ট্রি বাই', 'Created By') }}</th>
                        <th>{{ lang('নোট', 'Note') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($adjustments as $a)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><code>{{ $a->adjustment_no }}</code></td>
                            <td>{{ $a->adjustment_date->format('d M, Y') }}</td>
                            <td>
                                @if($a->type === 'increase')
                                    <span class="badge bg-success"><i class="fa-solid fa-arrow-up me-1"></i>{{ lang('বৃদ্ধি (Increase)', 'Increase') }}</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa-solid fa-arrow-down me-1"></i>{{ lang('হ্রাস (Decrease)', 'Decrease') }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $a->reason }}</span></td>
                            <td>
                                @foreach($a->items as $i)
                                    <div><strong>{{ $i->product->name ?? '' }}</strong>: {{ $i->quantity }} {{ $i->product->unit->short_name ?? '' }}</div>
                                @endforeach
                            </td>
                            <td>{{ $a->creator->name ?? 'System' }}</td>
                            <td>{{ $a->note ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Adjustment Modal -->
<div class="modal fade" id="createAdjustmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('stock.adjustments.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('নতুন স্টক এডজাস্টমেন্ট', 'New Stock Adjustment') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('তারিখ', 'Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="adjustment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('এডজাস্টমেন্টের ধরন', 'Adjustment Type') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="decrease">{{ lang('স্টক হ্রাস (Decrease / Loss)', 'Decrease (-)') }}</option>
                                <option value="increase">{{ lang('স্টক বৃদ্ধি (Increase / Correction)', 'Increase (+)') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('কারণ (Reason)', 'Reason') }} <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select" required>
                                <option value="Damage">Damage (ক্ষতিগ্রস্ত/নষ্ট)</option>
                                <option value="Expired">Expired (মেয়াদ উত্তীর্ণ)</option>
                                <option value="Lost">Lost (হারিয়ে গেছে)</option>
                                <option value="Correction">Correction (স্টক সংশোধন)</option>
                                <option value="Opening Stock">Opening Stock (প্রারম্ভিক স্টক)</option>
                                <option value="Other">Other (অন্যান্য)</option>
                            </select>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3 bg-light">
                        <h6 class="fw-bold mb-3 text-success">{{ lang('এডজাস্টমেন্টের পণ্য নির্বাচন', 'Select Product & Quantity') }}</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fs-7 fw-semibold">{{ lang('পণ্য (Product)', 'Product') }} <span class="text-danger">*</span></label>
                                <select name="items[0][product_id]" class="form-select" required>
                                    <option value="">-- পণ্য সিলেক্ট করুন --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (বর্তমান স্টক: {{ $p->current_stock }} {{ $p->unit->short_name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-7 fw-semibold">{{ lang('ব্যাচ (Batch)', 'Batch') }}</label>
                                <input type="text" name="items[0][batch_number]" class="form-control" placeholder="BATCH NO">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-7 fw-semibold">{{ lang('পরিমাণ (Quantity)', 'Quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('নোট / মন্তব্য', 'Note') }}</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="এডজাস্টমেন্টের কারণ বিস্তারিত লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">এডজাস্টমেন্ট সেভ করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
