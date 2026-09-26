@extends('layouts.app')

@section('title', lang('ড্যাশবোর্ড', 'Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success">
            <i class="fa-solid fa-gauge-high me-2"></i>{{ lang('বিজনেস ওভারভিউ ড্যাশবোর্ড', 'Business Overview Dashboard') }}
        </h4>
        <p class="text-muted mb-0 fs-7">{{ lang('আজকের বিক্রয়, ক্রয়, স্টক ভ্যালু ও আর্থিক অবস্থার সংক্ষিপ্ত চিত্র', 'Real-time sales, purchases, stock value & financial state') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.pos') }}" class="btn btn-emerald fw-bold shadow-sm">
            <i class="fa-solid fa-cash-register me-1"></i> {{ lang('POS কাউন্টার সেল', 'POS Terminal') }}
        </a>
        <a href="{{ route('purchases.create') }}" class="btn btn-outline-success fw-bold shadow-sm">
            <i class="fa-solid fa-plus-circle me-1"></i> {{ lang('নতুন পারচেজ', 'New Purchase') }}
        </a>
    </div>
</div>

<!-- Row 1: Primary Stats Cards -->
<div class="row g-3 mb-4">
    <!-- Today Sales -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-emerald-gradient">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-white-50 fs-7 fw-semibold text-uppercase">{{ lang('আজকের বিক্রি (Today Sales)', "Today's Sales") }}</span>
                    <h3 class="fw-bold text-white mb-0 mt-1">{{ format_currency($todaySales) }}</h3>
                    <small class="text-white-50">{{ lang('মোট বিক্রি:', 'Total Sales:') }} {{ format_currency($totalSales) }}</small>
                </div>
                <div class="stat-icon bg-white text-success shadow-sm">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Today Purchase -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-teal-gradient">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-white-50 fs-7 fw-semibold text-uppercase">{{ lang('আজকের ক্রয় (Today Purchase)', "Today's Purchase") }}</span>
                    <h3 class="fw-bold text-white mb-0 mt-1">{{ format_currency($todayPurchase) }}</h3>
                    <small class="text-white-50">{{ lang('মোট ক্রয়:', 'Total Purchase:') }} {{ format_currency($totalPurchase) }}</small>
                </div>
                <div class="stat-icon bg-white text-info shadow-sm">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Stock Value -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-white border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-7 fw-semibold text-uppercase">{{ lang('বর্তমান স্টক ভ্যালু', 'Current Stock Value') }}</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1">{{ format_currency($currentStockValue) }}</h3>
                    <small class="text-muted">{{ lang('লো স্টক অ্যালার্ট:', 'Low Stock Alerts:') }} <span class="badge bg-danger">{{ $lowStockCount }}</span></small>
                </div>
                <div class="stat-icon bg-emerald-gradient text-white shadow-sm">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customer Due -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-amber-gradient">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-white-50 fs-7 fw-semibold text-uppercase">{{ lang('গ্রাহকের বকেয়া (Customer Due)', 'Customer Due') }}</span>
                    <h3 class="fw-bold text-white mb-0 mt-1">{{ format_currency($totalCustomerDue) }}</h3>
                    <small class="text-white-50">{{ lang('পাওনা হিসাব', 'Receivable') }}</small>
                </div>
                <div class="stat-icon bg-white text-warning shadow-sm">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Secondary Stats -->
<div class="row g-3 mb-4">
    <!-- Supplier Due -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-rose-gradient">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-white-50 fs-7 fw-semibold text-uppercase">{{ lang('সরবরাহকারীর বকেয়া', 'Supplier Due') }}</span>
                    <h3 class="fw-bold text-white mb-0 mt-1">{{ format_currency($totalSupplierDue) }}</h3>
                    <small class="text-white-50">{{ lang('দেনা হিসাব', 'Payable') }}</small>
                </div>
                <div class="stat-icon bg-white text-danger shadow-sm">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Balance -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-white border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-7 fw-semibold text-uppercase">{{ lang('ক্যাশ ব্যালেন্স (Cash Balance)', 'Cash Balance') }}</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ format_currency($cashBalance) }}</h3>
                    <small class="text-muted">{{ lang('হাতে নগদ টাকা', 'Cash in hand') }}</small>
                </div>
                <div class="stat-icon bg-light text-success shadow-sm">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-white border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-7 fw-semibold text-uppercase">{{ lang('মোট খরচ (Total Expense)', 'Total Expense') }}</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1">{{ format_currency($totalExpense) }}</h3>
                    <small class="text-muted">{{ lang('দোকানের সকল খরচ', 'Shop expenses') }}</small>
                </div>
                <div class="stat-icon bg-light text-danger shadow-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat p-3 bg-white border">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-7 fw-semibold text-uppercase">{{ lang('নিট লাভ (Net Profit)', 'Net Profit') }}</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">{{ format_currency($netProfit) }}</h3>
                    <small class="text-muted">{{ lang('আনুমানিক নিট লাভ', 'Estimated Net Profit') }}</small>
                </div>
                <div class="stat-icon bg-light text-primary shadow-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Channel Quick Strip -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="p-3 bg-white border rounded d-flex align-items-center gap-3">
            <i class="fa-solid fa-building-columns text-primary fs-3"></i>
            <div>
                <small class="text-muted d-block">{{ lang('ব্যাংক ব্যালেন্স', 'Bank Balance') }}</small>
                <strong class="text-dark fs-6">{{ format_currency($bankBalance) }}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-white border rounded d-flex align-items-center gap-3">
            <i class="fa-solid fa-mobile-screen-button text-danger fs-3"></i>
            <div>
                <small class="text-muted d-block">{{ lang('bKash বিকাশ ব্যালেন্স', 'bKash Balance') }}</small>
                <strong class="text-dark fs-6">{{ format_currency($bkashBalance) }}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-white border rounded d-flex align-items-center gap-3">
            <i class="fa-solid fa-wallet text-warning fs-3"></i>
            <div>
                <small class="text-muted d-block">{{ lang('Nagad নগদ ব্যালেন্স', 'Nagad Balance') }}</small>
                <strong class="text-dark fs-6">{{ format_currency($nagadBalance) }}</strong>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-white border rounded d-flex align-items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-danger fs-3"></i>
            <div>
                <small class="text-muted d-block">{{ lang('মেয়াদ উত্তীর্ণ পণ্য', 'Expired Products') }}</small>
                <strong class="text-danger fs-6">{{ $expiredCount }} {{ lang('টি আইটেম', 'Items') }}</strong>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Interactive Charts -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chart-column text-success me-2"></i>{{ lang('গত ৬ মাসের ক্রয় ও বিক্রয় ট্রেন্ড', 'Sales vs Purchase Monthly Trend') }}</span>
                <span class="badge bg-light text-dark">Chart.js</span>
            </div>
            <div class="card-body">
                <canvas id="salesPurchaseChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="fa-solid fa-chart-pie text-info me-2"></i>{{ lang('পেমেন্ট মেথড অনুযায়ী ব্যালেন্স', 'Payment Methods Distribution') }}
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="paymentMethodChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Alert Tables & Recent Transactions -->
<div class="row g-3">
    <!-- Low Stock Alert -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3 text-warning border-bottom">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ lang('কম স্টকের পণ্যসমূহ (Low Stock Alerts)', 'Low Stock Alerts') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>{{ lang('পণ্য', 'Product') }}</th>
                                <th>{{ lang('ক্যাটাগরি', 'Category') }}</th>
                                <th>{{ lang('বর্তমান স্টক', 'Current Stock') }}</th>
                                <th>{{ lang('সর্বনিম্ন', 'Min Stock') }}</th>
                                <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @forelse($lowStockProducts as $p)
                                <tr>
                                    <td class="fw-bold">{{ $p->name }}</td>
                                    <td>{{ $p->category->name }}</td>
                                    <td class="text-danger fw-bold">{{ $p->current_stock }} {{ $p->unit->short_name }}</td>
                                    <td>{{ $p->min_stock }} {{ $p->unit->short_name }}</td>
                                    <td><span class="badge bg-danger">{{ lang('স্টক কম', 'Low Stock') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">{{ lang('কোন কম স্টকের পণ্য নেই', 'No low stock products') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Near Expiry Alert -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold py-3 text-danger border-bottom">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> {{ lang('মেয়াদ আসন্ন/উত্তীর্ণ অ্যালার্ট (Expiry Alert)', 'Expiry Alerts') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>{{ lang('পণ্য', 'Product') }}</th>
                                <th>{{ lang('ব্যাচ নং', 'Batch') }}</th>
                                <th>{{ lang('মেয়াদের তারিখ', 'Expiry Date') }}</th>
                                <th>{{ lang('স্টক', 'Stock') }}</th>
                                <th>{{ lang('স্ট্যাটাস', 'Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @forelse($nearExpiryList as $b)
                                <tr>
                                    <td class="fw-bold">{{ $b->product->name }}</td>
                                    <td><code>{{ $b->batch_number }}</code></td>
                                    <td>{{ $b->expiry_date->format('d M, Y') }}</td>
                                    <td>{{ $b->quantity }}</td>
                                    <td>
                                        @if($b->days_until_expiry < 0)
                                            <span class="badge bg-danger">{{ lang('মেয়াদ উত্তীর্ণ', 'Expired') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $b->days_until_expiry }} {{ lang('দিন বাকি', 'days left') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">{{ lang('কোন মেয়াদ উত্তীর্ণের অ্যালার্ট নেই', 'No expiry alerts') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-receipt text-success me-1"></i> {{ lang('সাম্প্রতিক বিক্রয় (Recent Sales)', 'Recent Sales') }}</span>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-link text-success p-0">{{ lang('সব দেখুন', 'View All') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>{{ lang('ইনভয়েস', 'Invoice') }}</th>
                                <th>{{ lang('গ্রাহক', 'Customer') }}</th>
                                <th>{{ lang('মোট', 'Total') }}</th>
                                <th>{{ lang('পরিশোধ', 'Paid') }}</th>
                                <th>{{ lang('বকেয়া', 'Due') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @foreach($recentSales as $s)
                                <tr>
                                    <td><a href="{{ route('sales.show', $s->id) }}" class="fw-bold text-success">{{ $s->invoice_no }}</a></td>
                                    <td>{{ $s->customer->name }}</td>
                                    <td class="fw-bold">{{ format_currency($s->net_amount) }}</td>
                                    <td class="text-success">{{ format_currency($s->paid_amount) }}</td>
                                    <td class="text-danger fw-bold">{{ format_currency($s->due_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-truck-ramp-box text-info me-1"></i> {{ lang('সাম্প্রতিক ক্রয় (Recent Purchases)', 'Recent Purchases') }}</span>
                <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-link text-info p-0">{{ lang('সব দেখুন', 'View All') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>{{ lang('চালান নং', 'Invoice') }}</th>
                                <th>{{ lang('সরবরাহকারী', 'Supplier') }}</th>
                                <th>{{ lang('মোট', 'Total') }}</th>
                                <th>{{ lang('পরিশোধ', 'Paid') }}</th>
                                <th>{{ lang('বকেয়া', 'Due') }}</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @foreach($recentPurchases as $p)
                                <tr>
                                    <td><a href="{{ route('purchases.show', $p->id) }}" class="fw-bold text-info">{{ $p->invoice_no }}</a></td>
                                    <td>{{ $p->supplier->name }}</td>
                                    <td class="fw-bold">{{ format_currency($p->net_amount) }}</td>
                                    <td class="text-success">{{ format_currency($p->paid_amount) }}</td>
                                    <td class="text-danger fw-bold">{{ format_currency($p->due_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sales vs Purchase Trend Chart
        const ctx1 = document.getElementById('salesPurchaseChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: "{{ lang('বিক্রি (Sales)', 'Sales') }}",
                        data: {!! json_encode($salesChartData) !!},
                        backgroundColor: '#0F5132',
                        borderRadius: 4
                    },
                    {
                        label: "{{ lang('ক্রয় (Purchases)', 'Purchases') }}",
                        data: {!! json_encode($purchaseChartData) !!},
                        backgroundColor: '#0dcaf0',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        // Payment Methods Pie Chart
        const ctx2 = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($pmLabels) !!},
                datasets: [{
                    data: {!! json_encode($pmBalances) !!},
                    backgroundColor: ['#198754', '#0d6efd', '#dc3545', '#ffc107', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endpush
