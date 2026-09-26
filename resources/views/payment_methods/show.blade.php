@extends('layouts.app')

@section('title', $paymentMethod->name . ' - ট্রানজেকশন লেজার')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-wallet me-2"></i>{{ $paymentMethod->name }}</h4>
        <p class="text-muted mb-0 fs-7">Acc: {{ $paymentMethod->account_number ?? 'N/A' }} | Holder: {{ $paymentMethod->account_holder ?? 'N/A' }}</p>
    </div>
    <a href="{{ route('payment-methods.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 bg-emerald-gradient text-white p-3">
            <span class="text-white-50 fs-7 fw-semibold">{{ lang('বর্তমান ব্যালেন্স (Current Balance)', 'Current Balance') }}</span>
            <h2 class="fw-bold mb-0 mt-1">{{ format_currency($paymentMethod->current_balance) }}</h2>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 p-3 bg-white border">
            <span class="text-muted fs-7 fw-semibold">{{ lang('প্রারম্ভিক ব্যালেন্স (Opening Balance)', 'Opening Balance') }}</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ format_currency($paymentMethod->opening_balance) }}</h3>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-receipt text-success me-2"></i>{{ lang('লেজার ট্রানজেকশনসমূহ (Transaction History)', 'Transaction History') }}</span>
        <button class="btn btn-sm btn-outline-secondary no-print" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> {{ lang('প্রিন্ট', 'Print') }}</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light fs-7">
                    <tr>
                        <th>#</th>
                        <th>{{ lang('তারিখ', 'Date') }}</th>
                        <th>{{ lang('ক্যাটাগরি', 'Category') }}</th>
                        <th>{{ lang('ধরন (Type)', 'Type') }}</th>
                        <th>{{ lang('রেফারেন্স', 'Reference') }}</th>
                        <th>{{ lang('জমা (Cash In ৳)', 'Cash In') }}</th>
                        <th>{{ lang('খরচ/প্রদান (Cash Out ৳)', 'Cash Out') }}</th>
                        <th>{{ lang('বিবরণ', 'Description') }}</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $t->date->format('d M, Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $t->category }}</span></td>
                            <td>
                                @if($t->type === 'cash_in')
                                    <span class="badge bg-success">{{ lang('ক্যাশ ইন (Cash In)', 'Cash In') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ lang('ক্যাশ আউট (Cash Out)', 'Cash Out') }}</span>
                                @endif
                            </td>
                            <td><code>{{ $t->reference ?? 'N/A' }}</code></td>
                            <td class="text-success fw-bold">{{ $t->type === 'cash_in' ? format_currency($t->amount) : '-' }}</td>
                            <td class="text-danger fw-bold">{{ $t->type === 'cash_out' ? format_currency($t->amount) : '-' }}</td>
                            <td>{{ $t->description ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">{{ lang('কোন ট্রানজেকশন পাওয়া যায়নি', 'No transactions found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
