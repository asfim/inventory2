@extends('layouts.app')

@section('title', lang('বিক্রয় তালিকা', 'Sales List'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-receipt me-2"></i>{{ lang('সেলস / বিক্রয় মেমো তালিকা', 'Sales Management') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('দোকানের সকল কাউন্টার সেল, বাকি মেমো ও গ্রাহক পেমেন্ট রেকর্ড', 'All sales invoices, customer dues & POS counter history') }}</p>
    </div>
    <a href="{{ route('sales.pos') }}" class="btn btn-warning fw-bold text-dark shadow-sm">
        <i class="fa-solid fa-cash-register me-1"></i> {{ lang('নতুন সেলস / POS কাউন্টার', 'POS Terminal / New Sale') }}
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('sales.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="customer_id" class="form-select">
                    <option value="">-- {{ lang('সকল গ্রাহক', 'All Customers') }} --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
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
                        <th>{{ lang('ইনভয়েস নম্বর', 'Invoice No') }}</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('গ্রাহক (Customer)', 'Customer') }}</th>
                        <th>{{ lang('সর্বমোট', 'Net Total') }}</th>
                        <th>{{ lang('পরিশোধ', 'Paid') }}</th>
                        <th>{{ lang('বকেয়া', 'Due') }}</th>
                        <th>{{ lang('মেথড', 'Method') }}</th>
                        <th>{{ lang('বিক্রেতা', 'Seller') }}</th>
                        <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                        <th class="text-end">{{ lang('অ্যাকশন', 'Action') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($sales as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('sales.show', $s->id) }}" class="fw-bold text-success text-decoration-none">
                                    {{ $s->invoice_no }}
                                </a>
                            </td>
                            <td>{{ $s->sale_date->format('d M, Y') }}</td>
                            <td>{{ $s->customer->name }}</td>
                            <td class="fw-bold">{{ format_currency($s->net_amount) }}</td>
                            <td class="text-success">{{ format_currency($s->paid_amount) }}</td>
                            <td class="text-danger fw-bold">{{ format_currency($s->due_amount) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $s->paymentMethod->name ?? 'Cash' }}</span></td>
                            <td>{{ $s->seller->name ?? 'System' }}</td>
                            <td>
                                @if($s->payment_status === 'paid')
                                    <span class="badge bg-success">{{ lang('পরিশোধিত', 'Paid') }}</span>
                                @elseif($s->payment_status === 'partial')
                                    <span class="badge bg-warning text-dark">{{ lang('আংশিক', 'Partial') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ lang('বকেয়া', 'Due') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sales.show', $s->id) }}" class="btn btn-outline-info" title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('sales.print', $s->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Invoice"><i class="fa-solid fa-print"></i></a>
                                    <a href="{{ route('sales.return', $s->id) }}" class="btn btn-outline-warning" title="Sales Return"><i class="fa-solid fa-rotate-left"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">{{ lang('কোন বিক্রয় রেকর্ড পাওয়া যায়নি', 'No sales records found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
