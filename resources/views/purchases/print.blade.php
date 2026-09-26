<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>পারচেজ চালান - {{ $purchase->invoice_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; color: #000; background: #fff; }
        .printable-invoice { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; }
        @media print {
            .printable-invoice { border: none; margin: 0; padding: 0; width: 100%; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="printable-invoice">
    <div class="text-center border-bottom pb-3 mb-4">
        <h3 class="fw-bold text-success mb-1">{{ App\Models\Setting::get('shop_name', 'সবুজ বাংলা এগ্রো মেডিসিন সেন্ট্রাল') }}</h3>
        <p class="mb-0 text-muted fs-7">{{ App\Models\Setting::get('shop_address', 'কৃষি মার্কেট, ধামরাই, ঢাকা') }}</p>
        <p class="mb-0 text-muted fs-7">মোবাইল: {{ App\Models\Setting::get('shop_phone', '01711-000000') }}</p>
        <h5 class="fw-bold text-uppercase mt-2 border d-inline-block px-3 py-1 bg-light">পারচেজ ইনভয়েস / ক্রয় রসিদ</h5>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold mb-1">সরবরাহকারী তথ্য (Supplier):</h6>
            <div><strong>{{ $purchase->supplier->name }}</strong></div>
            <div>{{ $purchase->supplier->company_name ?? '' }}</div>
            <div>মোবাইল: {{ $purchase->supplier->phone }}</div>
            <div>ঠিকানা: {{ $purchase->supplier->address ?? 'N/A' }}</div>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold mb-1">চালান বিবরণী:</h6>
            <div>ইনভয়েস নং: <strong>{{ $purchase->invoice_no }}</strong></div>
            <div>তারিখ: {{ $purchase->purchase_date->format('d/m/Y') }}</div>
            <div>পেমেন্ট মেথড: {{ $purchase->paymentMethod->name ?? 'Cash' }}</div>
        </div>
    </div>

    <table class="table table-bordered align-middle mb-4 fs-7">
        <thead class="table-light text-center">
            <tr>
                <th>#</th>
                <th class="text-start">পণ্যের বিবরণ</th>
                <th>ব্যাচ</th>
                <th>মেয়াদ</th>
                <th>পরিমাণ</th>
                <th>দর (৳)</th>
                <th>মোট (৳)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $item->product->name }}</strong></td>
                    <td class="text-center"><code>{{ $item->batch_number }}</code></td>
                    <td class="text-center">{{ $item->expiry_date->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->quantity }} {{ $item->product->unit->short_name ?? '' }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold">
            <tr>
                <td colspan="6" class="text-end">মোট সাবটোটাল:</td>
                <td class="text-end">{{ number_format($purchase->total_amount, 2) }} ৳</td>
            </tr>
            @if($purchase->discount_amount > 0)
            <tr>
                <td colspan="6" class="text-end">ডিসকাউন্ট:</td>
                <td class="text-end">- {{ number_format($purchase->discount_amount, 2) }} ৳</td>
            </tr>
            @endif
            <tr class="table-light fs-6">
                <td colspan="6" class="text-end">সর্বমোট (Net Total):</td>
                <td class="text-end">{{ number_format($purchase->net_amount, 2) }} ৳</td>
            </tr>
            <tr>
                <td colspan="6" class="text-end text-success">নগদ প্রদান (Paid):</td>
                <td class="text-end text-success">{{ number_format($purchase->paid_amount, 2) }} ৳</td>
            </tr>
            <tr>
                <td colspan="6" class="text-end text-danger">বকেয়া (Due):</td>
                <td class="text-end text-danger">{{ number_format($purchase->due_amount, 2) }} ৳</td>
            </tr>
        </tfoot>
    </table>

    <div class="row pt-5 mt-4 text-center fs-7">
        <div class="col-4">
            <div class="border-top pt-2">স্টোর কীপার স্বাক্ষর</div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">অডিট স্বাক্ষর</div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2">অনুমোদনকারী স্বাক্ষর</div>
        </div>
    </div>
</div>

</body>
</html>
