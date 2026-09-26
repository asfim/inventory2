@extends('layouts.app')

@section('title', 'POS - ফাস্ট কাউন্টার সেলস')

@section('content')
<div class="row g-3">
    <!-- Left Column: Product Search & Product Grid -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white"><i class="fa-solid fa-barcode"></i></span>
                            <input type="text" id="posSearchInput" class="form-control form-control-lg" placeholder="{{ lang('বারকোড স্ক্যান করুন বা পণ্যের নাম/সক্রিয় উপাদান লিখুন...', 'Scan Barcode or Search Product...') }}" autofocus>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="categoryFilter" class="form-select form-select-lg">
                            <option value="">-- {{ lang('সকল ক্যাটাগরি', 'All Categories') }} --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="card shadow-sm border-0" style="min-height: 520px; max-height: 600px; overflow-y: auto;">
            <div class="card-body p-3">
                <div class="row g-2" id="posProductGrid">
                    @foreach($products as $p)
                        <div class="col-md-4 col-6 product-item" data-category="{{ $p->category_id }}" data-search="{{ strtolower($p->name . ' ' . $p->product_code . ' ' . $p->barcode . ' ' . $p->active_ingredient) }}">
                            <div class="pos-product-card p-3 h-100 d-flex flex-column justify-content-between" onclick="addToCart({{ json_encode($p) }})">
                                <div>
                                    <span class="badge bg-light text-dark border fs-8 mb-1">{{ $p->category->name ?? 'Agro' }}</span>
                                    <h6 class="fw-bold text-success mb-1 text-truncate" title="{{ $p->name }}">{{ $p->name }}</h6>
                                    @if($p->active_ingredient)
                                        <small class="text-muted d-block text-truncate fs-8 mb-2"><i class="fa-solid fa-flask me-1"></i>{{ $p->active_ingredient }}</small>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-end mt-2 pt-2 border-top">
                                    <strong class="text-dark fs-6">{{ format_currency($p->selling_price) }}</strong>
                                    <span class="badge bg-success-subtle text-success border fs-8">Stock: {{ $p->current_stock }} {{ $p->unit->short_name ?? '' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Cart & Billing Panel -->
    <div class="col-lg-5">
        <form action="{{ route('sales.store') }}" method="POST" id="posForm">
            @csrf
            <input type="hidden" name="invoice_no" value="{{ $invoiceNo }}">
            <input type="hidden" name="sale_date" value="{{ date('Y-m-d') }}">

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-emerald-gradient text-white py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fa-solid fa-cart-shopping me-1"></i> {{ lang('বিক্রয় মেমো Cart', 'Sales Cart') }}</span>
                    <code>{{ $invoiceNo }}</code>
                </div>
                <div class="card-body p-3">
                    <!-- Customer Selector & Add Modal Trigger -->
                    <div class="row g-2 mb-2">
                        <div class="col-10">
                            <select name="customer_id" id="customerSelect" class="form-select" required>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#quickCustomerModal" title="Add Customer">
                                <i class="fa-solid fa-user-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Items Table -->
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm align-middle pos-cart-table mb-0">
                        <thead class="table-light fs-7">
                            <tr>
                                <th>{{ lang('পণ্য', 'Product') }}</th>
                                <th style="width: 25%;">{{ lang('পরিমাণ', 'Qty') }}</th>
                                <th style="width: 20%;">{{ lang('দর', 'Price') }}</th>
                                <th style="width: 22%;">{{ lang('মোট', 'Total') }}</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <tr id="emptyCartRow">
                                <td colspan="5" class="text-center py-4 text-muted">{{ lang('কার্ট খালি! পণ্য সিলেক্ট করুন।', 'Cart is empty! Click product to add.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Billing Calculations -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="row g-2 mb-2 fs-7">
                        <div class="col-6 fw-semibold text-muted">{{ lang('সাবটোটাল (Subtotal):', 'Subtotal:') }}</div>
                        <div class="col-6 text-end fw-bold fs-6" id="displaySubtotal">৳ 0.00</div>
                        <input type="hidden" name="total_amount" id="inputTotalAmount" value="0">
                    </div>

                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-6 fw-semibold fs-7">{{ lang('ডিসকাউন্ট (Discount ৳):', 'Discount ৳:') }}</div>
                        <div class="col-6">
                            <input type="number" step="0.01" name="discount_amount" id="inputDiscount" class="form-control form-control-sm text-end" value="0.00">
                        </div>
                    </div>

                    <div class="row g-2 mb-2 border-top pt-2">
                        <div class="col-6 fw-bold fs-6 text-success">{{ lang('সর্বমোট (Net Total):', 'Net Total:') }}</div>
                        <div class="col-6 text-end fw-bold fs-5 text-success" id="displayNetTotal">৳ 0.00</div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6 fw-semibold fs-7">{{ lang('পেমেন্ট মেথড:', 'Payment Method:') }}</div>
                        <div class="col-6">
                            <select name="payment_method_id" class="form-select form-select-sm" required>
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6 fw-semibold fs-7">{{ lang('নগদ প্রাপ্তি (Paid Amount):', 'Paid Amount:') }}</div>
                        <div class="col-6">
                            <input type="number" step="0.01" name="paid_amount" id="inputPaidAmount" class="form-control form-control-sm fw-bold text-end text-primary" value="0.00" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3 bg-light p-2 rounded">
                        <div class="col-6 fs-7 fw-semibold text-danger">{{ lang('গ্রাহকের বকেয়া (Due):', 'Due Amount:') }} <span id="displayDue">৳ 0.00</span></div>
                        <div class="col-6 text-end fs-7 fw-semibold text-success">{{ lang('ফেরত (Change):', 'Change:') }} <span id="displayChange">৳ 0.00</span></div>
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm text-dark" id="checkoutBtn">
                        <i class="fa-solid fa-print me-1"></i> {{ lang('সেলস কনফার্ম ও ক্যাশ মেমো (Checkout)', 'Complete Sale & Print') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="quickCustomerForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-emerald-gradient text-white py-2">
                    <h6 class="modal-title fw-bold">দ্রুত নতুন গ্রাহক যুক্ত করুন</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label fs-7 fw-semibold">গ্রাহকের নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fs-7 fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fs-7 fw-semibold">ঠিকানা</label>
                        <textarea name="address" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    <button type="submit" class="btn btn-sm btn-success fw-bold">যুক্ত করুন</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = {};

    function addToCart(product) {
        if (cart[product.id]) {
            if (cart[product.id].qty < product.current_stock) {
                cart[product.id].qty++;
            } else {
                Swal.fire({ icon: 'warning', title: 'স্টক সীমিত!', text: `এই পণ্যের সর্বোচ্চ স্টক ${product.current_stock}` });
            }
        } else {
            let batchNo = (product.active_batches && product.active_batches.length > 0) ? product.active_batches[0].batch_number : 'DEFAULT';
            cart[product.id] = {
                id: product.id,
                name: product.name,
                unit: product.unit ? product.unit.short_name : '',
                price: parseFloat(product.selling_price),
                stock: product.current_stock,
                batch_number: batchNo,
                qty: 1
            };
        }
        renderCart();
    }

    function updateQty(id, change) {
        if (cart[id]) {
            cart[id].qty += change;
            if (cart[id].qty <= 0) {
                delete cart[id];
            } else if (cart[id].qty > cart[id].stock) {
                cart[id].qty = cart[id].stock;
                Swal.fire({ icon: 'warning', title: 'স্টক সীমা অতিক্রম!', text: `সর্বোচ্চ স্টক ${cart[id].stock}` });
            }
        }
        renderCart();
    }

    function removeCartItem(id) {
        delete cart[id];
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const keys = Object.keys(cart);

        if (keys.length === 0) {
            tbody.innerHTML = `<tr id="emptyCartRow"><td colspan="5" class="text-center py-4 text-muted">{{ lang('কার্ট খালি! পণ্য সিলেক্ট করুন।', 'Cart is empty! Click product to add.') }}</td></tr>`;
            document.getElementById('displaySubtotal').innerText = '৳ 0.00';
            document.getElementById('displayNetTotal').innerText = '৳ 0.00';
            document.getElementById('inputTotalAmount').value = 0;
            document.getElementById('inputPaidAmount').value = '0.00';
            document.getElementById('displayDue').innerText = '৳ 0.00';
            document.getElementById('displayChange').innerText = '৳ 0.00';
            return;
        }

        let html = '';
        let subtotal = 0;

        keys.forEach((id, index) => {
            const item = cart[id];
            const itemTotal = item.qty * item.price;
            subtotal += itemTotal;

            html += `
                <tr>
                    <td>
                        <div class="fw-bold fs-7">${item.name}</div>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                        <input type="hidden" name="items[${index}][batch_number]" value="${item.batch_number}">
                        <input type="hidden" name="items[${index}][unit_price]" value="${item.price}">
                    </td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 90px;">
                            <button type="button" class="btn btn-outline-secondary px-1" onclick="updateQty(${item.id}, -1)">-</button>
                            <input type="number" name="items[${index}][quantity]" class="form-control text-center px-1" value="${item.qty}" readonly>
                            <button type="button" class="btn btn-outline-secondary px-1" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    </td>
                    <td>৳${item.price.toFixed(2)}</td>
                    <td class="fw-bold">৳${itemTotal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm text-danger p-0" onclick="removeCartItem(${item.id})"><i class="fa-solid fa-xmark"></i></button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        document.getElementById('displaySubtotal').innerText = '৳ ' + subtotal.toFixed(2);
        document.getElementById('inputTotalAmount').value = subtotal;

        calculateBill();
    }

    function calculateBill() {
        const gross = parseFloat(document.getElementById('inputTotalAmount').value) || 0;
        const discount = parseFloat(document.getElementById('inputDiscount').value) || 0;
        const net = Math.max(0, gross - discount);

        document.getElementById('displayNetTotal').innerText = '৳ ' + net.toFixed(2);

        const paid = parseFloat(document.getElementById('inputPaidAmount').value) || 0;
        const due = Math.max(0, net - paid);
        const change = Math.max(0, paid - net);

        document.getElementById('displayDue').innerText = '৳ ' + due.toFixed(2);
        document.getElementById('displayChange').innerText = '৳ ' + change.toFixed(2);
    }

    document.getElementById('inputDiscount').addEventListener('input', calculateBill);
    document.getElementById('inputPaidAmount').addEventListener('input', calculateBill);

    // Live Product Search & Category Filter
    document.getElementById('posSearchInput').addEventListener('input', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);

    function filterProducts() {
        const searchVal = document.getElementById('posSearchInput').value.toLowerCase();
        const catVal = document.getElementById('categoryFilter').value;

        document.querySelectorAll('.product-item').forEach(item => {
            const itemCat = item.dataset.category;
            const itemSearch = item.dataset.search;

            const matchesCat = (!catVal || itemCat === catVal);
            const matchesSearch = (!searchVal || itemSearch.includes(searchVal));

            if (matchesCat && matchesSearch) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    }

    // Quick Add Customer AJAX
    document.getElementById('quickCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('customers.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('customerSelect');
                const opt = new Option(`${data.customer.name} (${data.customer.phone})`, data.customer.id, true, true);
                select.add(opt);

                const modal = bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal'));
                modal.hide();
                Swal.fire({ icon: 'success', title: 'গ্রাহক যুক্ত করা হয়েছে!' });
            }
        });
    });

    // AJAX Form Submit & Print Receipt Modal
    document.getElementById('posForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (Object.keys(cart).length === 0) {
            Swal.fire({ icon: 'warning', title: 'কার্ট খালি!', text: 'অনুগ্রহ করে পণ্য যুক্ত করুন।' });
            return;
        }

        const formData = new FormData(this);

        fetch("{{ route('sales.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'বিক্রি সফল হয়েছে!',
                    text: 'ক্যাশ মেমো প্রিন্ট করবেন?',
                    showCancelButton: true,
                    confirmButtonText: 'মেমো প্রিন্ট করুন',
                    cancelButtonText: 'পরবর্তী সেলস'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(data.print_url, '_blank');
                    }
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'ত্রুটি!', text: data.message || 'বিক্রি সম্পন্ন করা যায়নি।' });
            }
        });
    });
</script>
@endpush
