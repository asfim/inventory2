@extends('layouts.app')

@section('title', lang('দোকানের খরচ ব্যবস্থাপনা', 'Expense Management'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-money-bill-transfer me-2"></i>{{ lang('দোকানের খরচ (Expense Management)', 'Expense Management') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকান ভাড়া, বিদ্যুৎ বিল, ইন্টারনেট, পরিবহন, বেতন ও অন্যান্য দৈনন্দিন খরচ', 'Shop rent, electricity, internet, transport, salaries & office expenses') }}</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#createExpenseCategoryModal">
            <i class="fa-solid fa-folder-plus me-1"></i> {{ lang('নতুন খরচের খাত', 'Add Category') }}
        </button>
        <button class="btn btn-emerald fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
            <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন খরচ এন্ট্রি', 'Add Expense') }}
        </button>
    </div>
</div>

<!-- Filter & Summary -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('expenses.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-7">{{ lang('খরচের খাত', 'Expense Category') }}</label>
                <select name="category_id" class="form-select">
                    <option value="">-- সকল খাত --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-7">{{ lang('শুরুর তারিখ', 'Start Date') }}</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-7">{{ lang('শেষ তারিখ', 'End Date') }}</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i> {{ lang('ফিল্টার করুন', 'Filter') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-receipt text-danger me-2"></i>{{ lang('খরচের তালিকা (Expense Log)', 'Expense Log History') }}</span>
        <span class="badge bg-danger fs-6">{{ lang('মোট খরচ:', 'Total Expense:') }} {{ format_currency($totalExpense) }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('খরচের খাত (Category)', 'Category') }}</th>
                        <th>{{ lang('পরিমাণ (Amount)', 'Amount') }}</th>
                        <th>{{ lang('পেমেন্ট মাধ্যম', 'Method') }}</th>
                        <th>{{ lang('বিবরণ', 'Description') }}</th>
                        <th>{{ lang('সংযুক্তি (Doc)', 'Attachment') }}</th>
                        <th>{{ lang('এন্ট্রি বাই', 'Created By') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @foreach($expenses as $e)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $e->expense_date->format('d M, Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $e->category->name ?? '' }}</span></td>
                            <td class="fw-bold text-danger fs-6">{{ format_currency($e->amount) }}</td>
                            <td><span class="badge bg-info-subtle text-dark border">{{ $e->paymentMethod->name ?? 'Cash' }}</span></td>
                            <td>{{ $e->description ?? 'N/A' }}</td>
                            <td>
                                @if($e->attachment)
                                    <a href="{{ asset('storage/' . $e->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-paperclip me-1"></i> ডকুমেন্ট</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $e->creator->name ?? 'System' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Expense Modal -->
<div class="modal fade" id="createExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white">
                    <h5 class="modal-title fw-bold">{{ lang('নতুন খরচ এন্ট্রি করুন', 'Add New Expense Record') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('খরচের তারিখ', 'Expense Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('খরচের খাত (Category)', 'Category') }} <span class="text-danger">*</span></label>
                        <select name="expense_category_id" class="form-select" required>
                            <option value="">-- খাত নির্বাচন করুন --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('টাকার পরিমাণ (Amount ৳)', 'Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold text-danger fs-5" placeholder="0.00" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পেমেন্ট প্রদান মাধ্যম', 'Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method_id" class="form-select" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} (ব্যালেন্স: {{ format_currency($pm->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('বিবরণ / রসিদ নোট', 'Description') }}</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="খরচের বিবরণ..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('রসিদ/বিল অ্যাটাচমেন্ট (Image / PDF)', 'Attachment') }}</label>
                        <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-success fw-bold">খরচ সংরক্ষণ করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createExpenseCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('expenses.categories.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">নতুন খরচের খাত যুক্ত করুন</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">খাতের নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="যেমন: Shop Rent, Transport, Electricity" required>
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
