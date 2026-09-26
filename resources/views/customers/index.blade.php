@extends('layouts.app')

@section('title', lang('গ্রাহক ব্যবস্থাপনা', 'Customer Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-users me-2"></i>{{ lang('গ্রাহক (Customers / Farmers)', 'Customer Management') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('কৃষক ও কাস্টমারদের তালিকা, বাকির হিসাব ও লেজার', 'Customer list, dues, sales history & ledgers') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
        <i class="fa-solid fa-user-plus me-1"></i> {{ lang('নতুন গ্রাহক যুক্ত করুন', 'Add Customer') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('গ্রাহকের নাম', 'Customer Name') }}</th>
                        <th>{{ lang('মোবাইল নম্বর', 'Phone') }}</th>
                        <th>{{ lang('ঠিকানা', 'Address') }}</th>
                        <th>{{ lang('ওপেনিং বাকি', 'Opening Due') }}</th>
                        <th>{{ lang('বর্তমান বাকি (Current Due)', 'Current Due') }}</th>
                        <th>{{ lang('মোট সেলস', 'Total Sales') }}</th>
                        <th>{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($customers as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('customers.show', $c->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $c->name }}
                                </a>
                            </td>
                            <td>{{ $c->phone }}</td>
                            <td>{{ $c->address ?? 'N/A' }}</td>
                            <td>{{ format_currency($c->opening_due) }}</td>
                            <td>
                                @if($c->current_due > 0)
                                    <span class="badge bg-warning text-dark fs-7">{{ format_currency($c->current_due) }}</span>
                                @else
                                    <span class="badge bg-success fs-7">{{ format_currency(0) }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $c->sales_count }} {{ lang('টি সেলস', 'Sales') }}</span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $c->id) }}" class="btn btn-outline-info" title="Ledger & Collect"><i class="fa-solid fa-book me-1"></i> {{ lang('লেজার ও আদায়', 'Ledger & Collect') }}</a>
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCustomerModal{{ $c->id }}"><i class="fa-solid fa-pen"></i></button>
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
@foreach($customers as $c)
    <div class="modal fade" id="editCustomerModal{{ $c->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('customers.update', $c->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">গ্রাহকের তথ্য সম্পাদনা</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $c->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $c->phone }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ইমেইল</label>
                            <input type="email" name="email" class="form-control" value="{{ $c->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ঠিকানা</label>
                            <textarea name="address" class="form-control" rows="2">{{ $c->address }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $c->status ? 'selected' : '' }}>সক্রিয়</option>
                                <option value="0" {{ !$c->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
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
<div class="modal fade" id="createCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন গ্রাহক যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">গ্রাহক/কৃষকের নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: আলহাজ্ব রফিকুল ইসলাম" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="01712-000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ইমেইল</label>
                        <input type="email" name="email" class="form-control" placeholder="customer@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ঠিকানা</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="গ্রাম, ইউনিয়ন, উপজেলা..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">প্রাথমিক বাকি (Opening Due ৳)</label>
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
