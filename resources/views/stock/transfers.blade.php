@extends('layouts.app')

@section('title', lang('স্টক ট্রান্সফার', 'Stock Transfers'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-right-left me-2"></i>{{ lang('ব্রাঞ্চ স্টক ট্রান্সফার (Stock Transfer)', 'Branch Stock Transfers') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('একাধিক শাখা থাকলে এক শাখা থেকে অন্য শাখায় পণ্য স্থানান্তর', 'Transfer products between branches in multi-branch store setup') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createTransferModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন ট্রান্সফার এন্ট্রি', 'New Transfer') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom">
        <i class="fa-solid fa-history me-1"></i> {{ lang('ব্রাঞ্চ ট্রান্সফার হিস্ট্রি (Transfer History Log)', 'Transfer History Log') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('ট্রান্সফার নং', 'Transfer No') }}</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('উৎস শাখা (From Branch)', 'From Branch') }}</th>
                        <th>{{ lang('গন্তব্য শাখা (To Branch)', 'To Branch') }}</th>
                        <th>{{ lang('আইটেমস', 'Transferred Items') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($transfers as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><code>{{ $t->transfer_no }}</code></td>
                            <td>{{ $t->transfer_date->format('d M, Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $t->fromBranch->name ?? '' }}</span></td>
                            <td><span class="badge bg-success-subtle text-success border">{{ $t->toBranch->name ?? '' }}</span></td>
                            <td>
                                @foreach($t->items as $i)
                                    <div><strong>{{ $i->product->name ?? '' }}</strong>: {{ $i->quantity }}</div>
                                @endforeach
                            </td>
                            <td><span class="badge bg-success">{{ lang('সম্পন্ন', 'Completed') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">{{ lang('কোন ট্রান্সফার রেকর্ড পাওয়া যায়নি', 'No transfer records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Transfer Modal -->
<div class="modal fade" id="createTransferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('stock.transfers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('নতুন স্টক ট্রান্সফার', 'New Branch Stock Transfer') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('ট্রান্সফারের তারিখ', 'Transfer Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('উৎস শাখা (From Branch)', 'From Branch') }} <span class="text-danger">*</span></label>
                            <select name="from_branch_id" class="form-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('গন্তব্য শাখা (To Branch)', 'To Branch') }} <span class="text-danger">*</span></label>
                            <select name="to_branch_id" class="form-select" required>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ !$b->is_main ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border rounded p-3 mb-3 bg-light">
                        <h6 class="fw-bold mb-3 text-success">{{ lang('পণ্য ও পরিমাণ নির্বাচন', 'Transferred Product') }}</h6>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <label class="form-label fs-7 fw-semibold">{{ lang('পণ্য (Product)', 'Product') }} <span class="text-danger">*</span></label>
                                <select name="items[0][product_id]" class="form-select" required>
                                    <option value="">-- পণ্য সিলেক্ট করুন --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (বর্তমান মজুদ: {{ $p->current_stock }} {{ $p->unit->short_name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-7 fw-semibold">{{ lang('পরিমাণ (Quantity)', 'Quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('নোট / বিবরণ', 'Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="ট্রান্সফারের বিবরণ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">ট্রান্সফার সম্পন্ন করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
