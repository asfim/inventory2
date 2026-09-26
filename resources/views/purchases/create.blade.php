@extends('layouts.app')

@section('title', lang('নতুন ক্রয় চালান', 'New Purchase Invoice'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-success"><i class="fa-solid fa-cart-flatbed me-2"></i>{{ lang('নতুন স্টক ক্রয় চালান এন্ট্রি (New Purchase)', 'New Purchase Entry') }}</h4>
        <p class="text-muted mb-0 fs-7">{{ lang('একই চালানে একাধিক পণ্য যুক্ত করে স্টক স্বয়ংক্রিয়ভাবে বৃদ্ধি করুন', 'Add multiple products to auto-increment inventory stock') }}</p>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> {{ lang('তালিকায় ফিরে যান', 'Back to List') }}
    </a>
</div>

<form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
    @csrf
    <div class="row g-4 mb-4">
        <!-- Top Purchase Info -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-emerald-gradient text-white fw-bold">
                    <i class="fa-solid fa-file-invoice me-1"></i> {{ lang('পারচেজ মেমো প্রাথমিক তথ্য', 'Purchase Basic Details') }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('চালান নম্বর (Invoice No)', 'Invoice Number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_no" class="form-control fw-bold" value="{{ $invoiceNo }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('ক্রয়ের তারিখ', 'Purchase Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">{{ lang('সরবরাহকারী (Supplier)', 'Supplier') }} <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">-- সরবরাহকারী নির্বাচন করুন --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->company_name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">
                    <i class="fa-solid fa-wallet me-1"></i> {{ lang('পেমেন্ট ও মাধ্যম', 'Payment & Method') }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ lang('পেমেন্ট মাধ্যম (Payment Method)', 'Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method_id" class="form-select" required>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} (ব্যালেন্স: {{ format_currency($pm->current_balance) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold py-3 border-bottom d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-list-check text-success me-1"></i> {{ lang('ক্রয়কৃত পণ্যের তালিকা (Multiple Products)', 'Purchased Products List') }}</span>
            <button type="button" class="btn btn-sm btn-success fw-bold" id="addRowBtn">
                <i class="fa-solid fa-plus me-1"></i> {{ lang('পণ্য লাইন যুক্ত করুন', 'Add Item Line') }}
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="purchaseItemsTable">
                    <thead class="table-light fs-7">
                        <tr>
                            <th style="width: 25%;">{{ lang('পণ্য নির্বাচন (Product)', 'Product') }} <span class="text-danger">*</span></th>
                            <th style="width: 12%;">{{ lang('ব্যাচ নম্বর (Batch)', 'Batch No') }} <span class="text-danger">*</span></th>
                            <th style="width: 13%;">{{ lang('মেয়াদের তারিখ (Expiry)', 'Expiry Date') }} <span class="text-danger">*</span></th>
                            <th style="width: 10%;">{{ lang('পরিমাণ (Qty)', 'Qty') }} <span class="text-danger">*</span></th>
                            <th style="width: 12%;">{{ lang('একক ক্রয়মূল্য (৳)', 'Purchase Price') }} <span class="text-danger">*</span></th>
                            <th style="width: 12%;">{{ lang('বিক্রয় মূল্য (৳)', 'Selling Price') }} <span class="text-danger">*</span></th>
                            <th style="width: 11%;">{{ lang('মোট টাকা (৳)', 'Subtotal') }}</th>
                            <th style="width: 5%;" class="text-center"><i class="fa-solid fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="purchaseItemsBody">
                        <!-- Dynamic Rows injected via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Calculation Summary & Submit -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <label class="form-label fw-semibold">{{ lang('নোট / বিবরণ', 'Notes / Remarks') }}</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="পারচেজ সংক্রান্ত বিশদ বিবরণ লিখুন..."></textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('পণ্যসমূহের মোট (Gross Total ৳)', 'Gross Total') }}</label>
                            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control fw-bold fs-5" readonly value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('ডিসকাউন্ট ছাড় (Discount ৳)', 'Discount Amount') }}</label>
                            <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('ট্যাক্স/ভ্যাট (Tax ৳)', 'Tax Amount') }}</label>
                            <input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('নিট প্রদেয় মোট (Net Amount ৳)', 'Net Amount') }}</label>
                            <input type="number" step="0.01" id="net_amount" class="form-control fw-bold text-success fs-5" readonly value="0.00">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('নগদ পরিশোধ (Paid Amount ৳)', 'Paid Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control fw-bold text-primary fs-5" value="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ lang('সরবরাহকারীর বকেয়া (Due Amount ৳)', 'Due Amount') }}</label>
                            <input type="number" step="0.01" id="due_amount" class="form-control fw-bold text-danger fs-5" readonly value="0.00">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-emerald btn-lg w-100 fw-bold mt-4 shadow">
                        <i class="fa-solid fa-check-circle me-1"></i> {{ lang('পারচেজ কনফার্ম করুন (Confirm Purchase)', 'Confirm & Update Stock') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const productsData = {!! json_encode($products) !!};

    function createRow(index) {
        let options = '<option value="">-- পণ্য নির্বাচন --</option>';
        productsData.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.purchase_price}" data-selling="${p.selling_price}">${p.name} (Code: ${p.product_code})</option>`;
        });

        const nextYear = new Date();
        nextYear.setFullYear(nextYear.getFullYear() + 2);
        const defaultExpiry = nextYear.toISOString().split('T')[0];

        return `
            <tr id="row_${index}">
                <td>
                    <select name="items[${index}][product_id]" class="form-select product-select" data-index="${index}" required>
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${index}][batch_number]" class="form-control" value="BATCH-${Date.now().toString().slice(-4)}" required>
                </td>
                <td>
                    <input type="date" name="items[${index}][expiry_date]" class="form-control" value="${defaultExpiry}" required>
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity]" class="form-control qty-input" data-index="${index}" value="1" min="1" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][unit_price]" class="form-control price-input" data-index="${index}" value="0.00" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${index}][selling_price]" class="form-control selling-input" data-index="${index}" value="0.00" required>
                </td>
                <td>
                    <input type="number" step="0.01" id="subtotal_${index}" class="form-control subtotal-field" value="0.00" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="fa-solid fa-times"></i></button>
                </td>
            </tr>
        `;
    }

    document.addEventListener("DOMContentLoaded", function() {
        let rowCount = 0;

        function addRow() {
            document.getElementById('purchaseItemsBody').insertAdjacentHTML('beforeend', createRow(rowCount));
            rowCount++;
        }

        // Add first row by default
        addRow();

        document.getElementById('addRowBtn').addEventListener('click', addRow);

        document.getElementById('purchaseItemsBody').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row-btn')) {
                const tr = e.target.closest('tr');
                if (document.querySelectorAll('#purchaseItemsBody tr').length > 1) {
                    tr.remove();
                    calculateTotals();
                } else {
                    alert('কমপক্ষে একটি পণ্য থাকতে হবে!');
                }
            }
        });

        document.getElementById('purchaseItemsBody').addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const selected = e.target.options[e.target.selectedIndex];
                const index = e.target.dataset.index;
                const pPrice = selected.dataset.price || 0;
                const sPrice = selected.dataset.selling || 0;

                const tr = document.getElementById(`row_${index}`);
                tr.querySelector('.price-input').value = parseFloat(pPrice).toFixed(2);
                tr.querySelector('.selling-input').value = parseFloat(sPrice).toFixed(2);

                updateRowSubtotal(index);
            }
        });

        document.getElementById('purchaseItemsBody').addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                const index = e.target.dataset.index;
                updateRowSubtotal(index);
            }
        });

        function updateRowSubtotal(index) {
            const tr = document.getElementById(`row_${index}`);
            if (!tr) return;
            const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
            const price = parseFloat(tr.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            document.getElementById(`subtotal_${index}`).value = subtotal.toFixed(2);
            calculateTotals();
        }

        function calculateTotals() {
            let gross = 0;
            document.querySelectorAll('.subtotal-field').forEach(el => {
                gross += parseFloat(el.value) || 0;
            });

            document.getElementById('total_amount').value = gross.toFixed(2);

            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
            const net = Math.max(0, gross - discount + tax);

            document.getElementById('net_amount').value = net.toFixed(2);

            const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
            const due = Math.max(0, net - paid);
            document.getElementById('due_amount').value = due.toFixed(2);
        }

        document.getElementById('discount_amount').addEventListener('input', calculateTotals);
        document.getElementById('tax_amount').addEventListener('input', calculateTotals);
        document.getElementById('paid_amount').addEventListener('input', calculateTotals);
    });
</script>
@endsection
