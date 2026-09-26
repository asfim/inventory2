<!DOCTYPE html>
<html lang="{{ session('locale', 'bn') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'কীটনাশক ও কৃষি ঔষধ ইনভেন্টরি ম্যানেজমেন্ট') - {{ App\Models\Setting::get('shop_name', 'Agro Med Inventory') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="no-print">
            <div class="sidebar-heading text-center d-flex align-items-center justify-content-center gap-2">
                <i class="fa-solid fa-leaf text-warning fs-4"></i>
                <span class="text-truncate fs-5">{{ lang('কৃষি এগ্রো ERP', 'Agro Med ERP') }}</span>
            </div>
            
            <div class="list-group list-group-flush py-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ active_nav('dashboard') }}">
                    <i class="fa-solid fa-chart-line text-emerald"></i>
                    <span>{{ lang('ড্যাশবোর্ড', 'Dashboard') }}</span>
                </a>

                <!-- POS / New Sale Callout -->
                <div class="px-3 my-2">
                    <a href="{{ route('sales.pos') }}" class="btn btn-warning w-100 fw-bold d-flex align-items-center justify-content-center gap-2 text-dark shadow-sm">
                        <i class="fa-solid fa-cash-register"></i>
                        <span>{{ lang('POS काउंटर সেল', 'POS Terminal') }}</span>
                    </a>
                </div>

                <!-- Sales Management -->
                <div class="sidebar-group-title">{{ lang('বিক্রয় ব্যবস্থাপনা', 'Sales Management') }}</div>
                <a href="{{ route('sales.index') }}" class="sidebar-nav-link {{ active_nav(['sales.index', 'sales.show', 'sales.return']) }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>{{ lang('বিক্রয় তালিকা (Sales)', 'Sales List') }}</span>
                </a>
                <a href="{{ route('sales.pos') }}" class="sidebar-nav-link {{ active_nav('sales.pos') }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>{{ lang('নতুন বিক্রয় (New Sale)', 'New Sale / POS') }}</span>
                </a>

                <!-- Purchase Management -->
                <div class="sidebar-group-title">{{ lang('ক্রয় ব্যবস্থাপনা', 'Purchase Management') }}</div>
                <a href="{{ route('purchases.index') }}" class="sidebar-nav-link {{ active_nav(['purchases.index', 'purchases.show', 'purchases.return']) }}">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>{{ lang('ক্রয় তালিকা (Purchases)', 'Purchase List') }}</span>
                </a>
                <a href="{{ route('purchases.create') }}" class="sidebar-nav-link {{ active_nav('purchases.create') }}">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>{{ lang('নতুন ক্রয় (New Purchase)', 'New Purchase') }}</span>
                </a>

                <!-- Stock Management -->
                <div class="sidebar-group-title">{{ lang('স্টক ও মেয়াদ', 'Stock & Expiry') }}</div>
                <a href="{{ route('stock.overview') }}" class="sidebar-nav-link {{ active_nav('stock.overview') }}">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>{{ lang('স্টক ওভারভিউ', 'Stock Overview') }}</span>
                </a>
                <a href="{{ route('stock.adjustments') }}" class="sidebar-nav-link {{ active_nav('stock.adjustments') }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>{{ lang('স্টক এডজাস্টমেন্ট', 'Stock Adjustment') }}</span>
                </a>
                <a href="{{ route('stock.transfers') }}" class="sidebar-nav-link {{ active_nav('stock.transfers') }}">
                    <i class="fa-solid fa-right-left"></i>
                    <span>{{ lang('স্টক ট্রান্সফার', 'Stock Transfer') }}</span>
                </a>
                <a href="{{ route('expiry.index') }}" class="sidebar-nav-link {{ active_nav('expiry.index') }}">
                    <i class="fa-solid fa-calendar-xmark text-warning"></i>
                    <span>{{ lang('মেয়াদের তালিকা (Expiry)', 'Expiry Management') }}</span>
                </a>

                <!-- Accounts & Cashbook -->
                <div class="sidebar-group-title">{{ lang('হিসাব ও আর্থিক', 'Accounts & Cash') }}</div>
                <a href="{{ route('cashbook.index') }}" class="sidebar-nav-link {{ active_nav('cashbook.index') }}">
                    <i class="fa-solid fa-book-journal-whills"></i>
                    <span>{{ lang('ক্যাশ বুক (Cash Book)', 'Cash Book') }}</span>
                </a>
                <a href="{{ route('investments.index') }}" class="sidebar-nav-link {{ active_nav('investments.index') }}">
                    <i class="fa-solid fa-sack-dollar"></i>
                    <span>{{ lang('মূলধন বিনিয়োগ', 'Investments') }}</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="sidebar-nav-link {{ active_nav('expenses.index') }}">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>{{ lang('দোকানের খরচ (Expense)', 'Expenses') }}</span>
                </a>

                <!-- Stakeholders -->
                <div class="sidebar-group-title">{{ lang('পার্টি ও কর্মচারী', 'Parties & Staff') }}</div>
                <a href="{{ route('suppliers.index') }}" class="sidebar-nav-link {{ active_nav(['suppliers.index', 'suppliers.show']) }}">
                    <i class="fa-solid fa-building-user"></i>
                    <span>{{ lang('সরবরাহকারী (Suppliers)', 'Suppliers') }}</span>
                </a>
                <a href="{{ route('customers.index') }}" class="sidebar-nav-link {{ active_nav(['customers.index', 'customers.show']) }}">
                    <i class="fa-solid fa-users"></i>
                    <span>{{ lang('গ্রাহক (Customers)', 'Customers') }}</span>
                </a>
                <a href="{{ route('employees.index') }}" class="sidebar-nav-link {{ active_nav(['employees.index', 'employees.show']) }}">
                    <i class="fa-solid fa-user-tie"></i>
                    <span>{{ lang('কর্মচারী ও বেতন', 'Employees & Salary') }}</span>
                </a>

                <!-- Master Data & Settings -->
                <div class="sidebar-group-title">{{ lang('পণ্য ও সেটিংস', 'Products & Settings') }}</div>
                <a href="{{ route('products.index') }}" class="sidebar-nav-link {{ active_nav(['products.index', 'products.create', 'products.edit', 'products.show']) }}">
                    <i class="fa-solid fa-flask-vial"></i>
                    <span>{{ lang('পণ্য তালিকা (Products)', 'Products List') }}</span>
                </a>
                <a href="{{ route('categories.index') }}" class="sidebar-nav-link {{ active_nav('categories.index') }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>{{ lang('ক্যাটাগরি', 'Categories') }}</span>
                </a>
                <a href="{{ route('brands.index') }}" class="sidebar-nav-link {{ active_nav('brands.index') }}">
                    <i class="fa-solid fa-copyright"></i>
                    <span>{{ lang('কোম্পানি/ব্র্যান্ড', 'Brands') }}</span>
                </a>
                <a href="{{ route('units.index') }}" class="sidebar-nav-link {{ active_nav('units.index') }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>{{ lang('পরিমাপের ইউনিট', 'Units') }}</span>
                </a>
                <a href="{{ route('payment-methods.index') }}" class="sidebar-nav-link {{ active_nav(['payment-methods.index', 'payment-methods.show']) }}">
                    <i class="fa-solid fa-wallet"></i>
                    <span>{{ lang('পেমেন্ট মেথড', 'Payment Methods') }}</span>
                </a>
                <a href="{{ route('users.index') }}" class="sidebar-nav-link {{ active_nav('users.index') }}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>{{ lang('ইউজার ও রোলস', 'Users & Roles') }}</span>
                </a>
                <a href="{{ route('settings.index') }}" class="sidebar-nav-link {{ active_nav('settings.index') }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>{{ lang('সিস্টেম সেটিং', 'Settings') }}</span>
                </a>

                <!-- Reports -->
                <div class="sidebar-group-title">{{ lang('রিপোর্টস (Reports)', 'Reports') }}</div>
                <a href="{{ route('reports.sales') }}" class="sidebar-nav-link {{ active_nav('reports.sales') }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>{{ lang('সেলস রিপোর্ট', 'Sales Report') }}</span>
                </a>
                <a href="{{ route('reports.purchases') }}" class="sidebar-nav-link {{ active_nav('reports.purchases') }}">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>{{ lang('পারচেজ রিপোর্ট', 'Purchase Report') }}</span>
                </a>
                <a href="{{ route('reports.stock') }}" class="sidebar-nav-link {{ active_nav('reports.stock') }}">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>{{ lang('স্টক রিপোর্ট', 'Stock Report') }}</span>
                </a>
                <a href="{{ route('reports.financial') }}" class="sidebar-nav-link {{ active_nav('reports.financial') }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>{{ lang('আর্থিক রিপোর্ট (P&L)', 'Financial Report') }}</span>
                </a>
            </div>
        </div>

        <!-- Page Content Wrapper -->
        <div id="page-content-wrapper" class="flex-grow-1 d-flex flex-column min-vh-100">
            <!-- Top Navigation Bar -->
            <nav class="navbar navbar-expand-lg navbar-light top-navbar px-3 py-2 border-bottom no-print">
                <div class="container-fluid px-0">
                    <button class="btn btn-light border" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    <div class="ms-3 d-none d-md-block">
                        <h6 class="mb-0 fw-bold text-success">
                            <i class="fa-solid fa-store me-1"></i>
                            {{ App\Models\Setting::get('shop_name', 'সবুজ বাংলা এগ্রো মেডিসিন সেন্ট্রাল') }}
                        </h6>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <!-- Language Toggle -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-language text-success fs-5"></i>
                                <span>{{ session('locale', 'bn') == 'bn' ? 'বাংলা' : 'English' }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item fw-medium" href="{{ route('lang.switch', 'bn') }}">🇧🇩 বাংলা (Bangla)</a></li>
                                <li><a class="dropdown-item fw-medium" href="{{ route('lang.switch', 'en') }}">🇺🇸 English</a></li>
                            </ul>
                        </div>

                        <!-- Low Stock & Expiry Alerts badges -->
                        @php
                            $lowStockAlertCount = \App\Models\Product::whereColumn('current_stock', '<=', 'min_stock')->count();
                            $nearExpiryAlertCount = \App\Models\ProductBatch::where('quantity', '>', 0)->get()->filter(fn($b) => $b->days_until_expiry <= 60)->count();
                        @endphp

                        <a href="{{ route('stock.overview', ['status' => 'low']) }}" class="btn btn-sm btn-outline-warning position-relative" title="Low Stock Alerts">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            @if($lowStockAlertCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $lowStockAlertCount }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('expiry.index') }}" class="btn btn-sm btn-outline-danger position-relative" title="Expiry Alerts">
                            <i class="fa-solid fa-clock font-awesome"></i>
                            @if($nearExpiryAlertCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                                    {{ $nearExpiryAlertCount }}
                                </span>
                            @endif
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-emerald-gradient text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="d-none d-lg-block text-start">
                                    <div class="fw-bold text-dark fs-7 lh-1">{{ Auth::user()->name ?? 'User' }}</div>
                                    <small class="text-muted fs-8">{{ Auth::user()->role->display_name ?? 'Admin' }}</small>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile') }}">
                                        <i class="fa-solid fa-user-gear text-primary"></i>
                                        <span>{{ lang('প্রোফাইল সেটিংস', 'Profile Settings') }}</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                            <span>{{ lang('লগআউট (Logout)', 'Logout') }}</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Body Content -->
            <div class="container-fluid p-4 main-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-auto bg-white py-3 border-top text-center no-print">
                <div class="container">
                    <small class="text-muted">
                        © {{ date('Y') }} <strong>{{ App\Models\Setting::get('shop_name', 'সবুজ বাংলা এগ্রো মেডিসিন সেন্ট্রাল') }}</strong> | {{ lang('কীটনাশক ও কৃষি ঔষধ স্টক সফটওয়্যার', 'Pesticide & Agro Medicine Inventory Software') }}
                    </small>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.fn.dataTable.ext.errMode = 'none';
        $(document).ready(function() {
            // Sidebar Toggle
            $("#sidebarToggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
                $("#sidebar-wrapper").toggleClass("d-none");
            });

            // Auto init DataTables
            $('.datatable').DataTable({
                "language": {
                    "search": "{{ lang('খুঁজুন:', 'Search:') }}",
                    "lengthMenu": "{{ lang('প্রদর্শন _MENU_ টি', 'Show _MENU_ entries') }}",
                    "info": "{{ lang('দেখাচ্ছে _START_ থেকে _END_ মোট _TOTAL_ টির মধ্যে', 'Showing _START_ to _END_ of _TOTAL_ entries') }}",
                    "zeroRecords": "{{ lang('কোন তথ্য পাওয়া যায়নি', 'No matching records found') }}",
                    "emptyTable": "{{ lang('কোন তথ্য পাওয়া যায়নি', 'No data available in table') }}",
                    "paginate": {
                        "first": "{{ lang('প্রথম', 'First') }}",
                        "last": "{{ lang('শেষ', 'Last') }}",
                        "next": "{{ lang('পরবর্তী', 'Next') }}",
                        "previous": "{{ lang('পূর্ববর্তী', 'Previous') }}"
                    }
                }
            });

            // SweetAlert Notifications
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ lang("সফল!", "Success!") }}',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ lang("ত্রুটি!", "Error!") }}',
                    text: "{{ session('error') }}",
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
