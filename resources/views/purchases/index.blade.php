@extends('layouts.app')

@section('title', lang('ক্রয় তালিকা', 'Purchase List'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-truck-ramp-box me-2"></i>{{ lang('পারচেজ / ক্রয় তালিকা', 'Purchase Management') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('সরবরাহকারীদের থেকে ক্রয়কৃত ওষুধ, স্টক প্রবেশ ও চালানের বিবরণী', 'All purchase invoices, payments, supplier balances & returns') }}</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-emerald fw-bold shadow-sm">
        <i class="fa-solid fa-plus me-1"></i> {{ lang('নতুন ক্রয় (New Purchase)', 'New Purchase') }}
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('purchases.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="supplier_id" class="form-select">
                    <option value="">-- {{ lang('সকল সরবরাহকারী', 'All Suppliers') }} --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100 fw-semibold"><i class="fa-solid fa-filter me-1"></i> {{ lang('ফিল্টার', 'Filter') }}</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('চালান নম্বর (Invoice)', 'Invoice No') }}</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('সরবরাহকারী', 'Supplier') }}</th>
                        <th>{{ lang('মোট টাকা', 'Net Amount') }}</th>
                        <th>{{ lang('পরিশোধ', 'Paid') }}</th>
                        <th>{{ lang('বকেয়া', 'Due') }}</th>
                        <th>{{ lang('পেমেন্ট মেথড', 'Method') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($purchases as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('purchases.show', $p->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $p->invoice_no }}
                                </a>
                            </td>
                            <td>{{ $p->purchase_date->format('d M, Y') }}</td>
                            <td>{{ $p->supplier->name }}</td>
                            <td class="fw-bold">{{ format_currency($p->net_amount) }}</td>
                            <td class="text-success">{{ format_currency($p->paid_amount) }}</td>
                            <td class="text-danger fw-bold">{{ format_currency($p->due_amount) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $p->paymentMethod->name ?? 'N/A' }}</span></td>
                            <td>
                                @if($p->payment_status === 'paid')
                                    <span class="badge bg-success">{{ lang('পরিশোধিত', 'Paid') }}</span>
                                @elseif($p->payment_status === 'partial')
                                    <span class="badge bg-warning text-dark">{{ lang('আংশিক', 'Partial') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ lang('বকেয়া', 'Due') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('purchases.show', $p->id) }}" class="btn btn-outline-info" title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('purchases.print', $p->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Invoice"><i class="fa-solid fa-print"></i></a>
                                    <a href="{{ route('purchases.return', $p->id) }}" class="btn btn-outline-warning" title="Return Items"><i class="fa-solid fa-rotate-left"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">{{ lang('কোন পারচেজ রেকর্ড পাওয়া যায়নি', 'No purchase records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
