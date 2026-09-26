@extends('layouts.app')

@section('title', lang('সরবরাহকারী ব্যবস্থাপনা', 'Supplier Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-building-user me-2"></i>{{ lang('সরবরাহকারী (Suppliers)', 'Supplier Management') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('যেসব কোম্পানি বা পরিবেশক থেকে ওষুধ ক্রয় করা হয়', 'Supplier details, balances & purchase ledgers') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন সরবরাহকারী যুক্ত করুন', 'Add Supplier') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('সরবরাহকারীর নাম', 'Supplier Name') }}</th>
                        <th>{{ lang('কোম্পানি', 'Company') }}</th>
                        <th>{{ lang('মোবাইল', 'Phone') }}</th>
                        <th>{{ lang('ওপেনিং বকেয়া', 'Opening Due') }}</th>
                        <th>{{ lang('বর্তমান বকেয়া (Current Due)', 'Current Due') }}</th>
                        <th>{{ lang('মোট ক্রয়', 'Total Purchases') }}</th>
                        <th>{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($suppliers as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('suppliers.show', $s->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $s->name }}
                                </a>
                            </td>
                            <td>{{ $s->company_name ?? 'N/A' }}</td>
                            <td>{{ $s->phone }}</td>
                            <td>{{ format_currency($s->opening_due) }}</td>
                            <td>
                                @if($s->current_due > 0)
                                    <span class="badge bg-danger fs-7">{{ format_currency($s->current_due) }}</span>
                                @else
                                    <span class="badge bg-success fs-7">{{ format_currency(0) }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $s->purchases_count }} {{ lang('টি ক্রয়', 'Purchases') }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('suppliers.show', $s->id) }}" class="btn btn-outline-info" title="Ledger & Pay"><i class="fa-solid fa-book me-1"></i> {{ lang('লেজার ও পরিশোধ', 'Ledger & Pay') }}</a>
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $s->id }}"><i class="fa-solid fa-pen"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modals (outside table) -->
@foreach($suppliers as $s)
    <div class="modal fade" id="editSupplierModal{{ $s->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('suppliers.update', $s->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">সরবরাহকারী তথ্য সম্পাদনা</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $s->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">কোম্পানির নাম</label>
                            <input type="text" name="company_name" class="form-control" value="{{ $s->company_name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $s->phone }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ইমেইল</label>
                            <input type="email" name="email" class="form-control" value="{{ $s->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ঠিকানা</label>
                            <textarea name="address" class="form-control" rows="2">{{ $s->address }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $s->status ? 'selected' : '' }}>সক্রিয়</option>
                                <option value="0" {{ !$s->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                        <button type="submit" class="btn btn-success fw-bold">আপডেট করুন</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach

<!-- Create Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন সরবরাহকারী যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">সরবরাহকারীর নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: সিনজেন্টা বাংলাদেশ লিমিটেড" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">কোম্পানির নাম</label>
                        <input type="text" name="company_name" class="form-control" placeholder="যেমন: Syngenta BD Ltd.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="01700-000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ইমেইল</label>
                        <input type="email" name="email" class="form-control" placeholder="supplier@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ঠিকানা</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="কোম্পানির ঠিকানা..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">প্রাথমিক বকেয়া (Opening Due ৳)</label>
                        <input type="number" step="0.01" name="opening_due" class="form-control" value="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">সংরক্ষণ করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
