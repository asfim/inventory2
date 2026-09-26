@extends('layouts.app')

@section('title', lang('কর্মচারী ব্যবস্থাপনা', 'Employee Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-user-tie me-2"></i>{{ lang('কর্মচারী ও বেতন হিসাব', 'Employees & Salary') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের বিক্রয়কর্মীদের তথ্য, বেতন পরিষদ ও মাসিক বেতন হিস্ট্রি', 'Staff list, salary disbursemnt and logs') }}</p>
    </div>
    <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
        <i class="fa-solid fa-user-plus me-1"></i> {{ lang('নতুন কর্মচারী যুক্ত করুন', 'Add Employee') }}
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('কর্মচারীর নাম', 'Employee Name') }}</th>
                        <th>{{ lang('পদবী', 'Designation') }}</th>
                        <th>{{ lang('মোবাইল', 'Phone') }}</th>
                        <th>{{ lang('যোগদানের তারিখ', 'Joining Date') }}</th>
                        <th>{{ lang('মাসিক বেতন', 'Salary') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($employees as $e)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('employees.show', $e->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $e->name }}
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $e->designation }}</span></td>
                            <td>{{ $e->phone }}</td>
                            <td>{{ $e->joining_date->format('d M, Y') }}</td>
                            <td class="fw-bold">{{ format_currency($e->salary) }}</td>
                            <td>
                                @if($e->status)
                                    <span class="badge bg-success">সক্রিয়</span>
                                @else
                                    <span class="badge bg-secondary">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('employees.show', $e->id) }}" class="btn btn-outline-info" title="Salary Ledger"><i class="fa-solid fa-money-bill me-1"></i> {{ lang('বেতন পরিষদ', 'Pay Salary') }}</a>
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $e->id }}"><i class="fa-solid fa-pen"></i></button>
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
@foreach($employees as $e)
    <div class="modal fade" id="editEmployeeModal{{ $e->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('employees.update', $e->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">কর্মচারী তথ্য সম্পাদনা</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $e->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">পদবী <span class="text-danger">*</span></label>
                            <input type="text" name="designation" class="form-control" value="{{ $e->designation }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $e->phone }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ইমেইল</label>
                            <input type="email" name="email" class="form-control" value="{{ $e->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">যোগদানের তারিখ</label>
                            <input type="date" name="joining_date" class="form-control" value="{{ $e->joining_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">মাসিক নির্দিষ্ট বেতন (৳)</label>
                            <input type="number" step="0.01" name="salary" class="form-control" value="{{ $e->salary }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $e->status ? 'selected' : '' }}>সক্রিয়</option>
                                <option value="0" {{ !$e->status ? 'selected' : '' }}>নিষ্ক্রিয়</option>
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
<div class="modal fade" id="createEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">নতুন কর্মচারী যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">কর্মচারীর নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: মোঃ তারেক রহমান" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">পদবী (Designation) <span class="text-danger">*</span></label>
                        <input type="text" name="designation" class="form-control" placeholder="যেমন: Sales Executive, Store Manager" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" placeholder="01755-000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ইমেইল</label>
                        <input type="email" name="email" class="form-control" placeholder="employee@agromed.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">যোগদানের তারিখ <span class="text-danger">*</span></label>
                        <input type="date" name="joining_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">মাসিক বেতন (Salary ৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="salary" class="form-control" placeholder="0.00" required>
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
