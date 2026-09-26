<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>বিক্রয় ক্যাশ মেমো - {{ $sale->invoice_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; color: #000; background: #fff; }
        .printable-invoice { max-width: 800px; margin: 20px auto; padding: 25px; border: 1px dashed #ccc; }
        @media print {
            .printable-invoice { border: none; margin: 0; padding: 0; width: 100%; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="printable-invoice">
    <!-- Header -->
    <div class="text-center border-bottom pb-3 mb-3">
        <h3 class="fw-bold text-success mb-1">{{ App\Models\Setting::get('shop_name', 'সবুজ বাংলা এগ্রো মেডিসিন সেন্ট্রাল') }}</h3>
        <p class="mb-0 text-muted fs-7">{{ App\Models\Setting::get('shop_address', 'কৃষি মার্কেট, ধামরাই, ঢাকা') }}</p>
        <p class="mb-0 text-muted fs-7">ফোন: {{ App\Models\Setting::get('shop_phone', '01711-000000') }}</p>
        <h5 class="fw-bold text-uppercase mt-2 border d-inline-block px-3 py-1 bg-light">বিক্রয় ক্যাশ মেমো / Sales Invoice</h5>
    </div>

    <!-- Customer & Invoice Info -->
    <div class="row mb-3 fs-7">
        <div class="col-6">
            <h6 class="fw-bold mb-1">গ্রাহকের নাম (Customer):</h6>
            <div><strong>{{ $sale->customer->name }}</strong></div>
            <div>মোবাইল: {{ $sale->customer->phone }}</div>
            <div>ঠিকানা: {{ $sale->customer->address ?? 'N/A' }}</div>
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold mb-1">মেমো বিবরণী:</h6>
            <div>মেমো নং: <strong>{{ $sale->invoice_no }}</strong></div>
            <div>তারিখ: {{ $sale->sale_date->format('d/m/Y') }}</div>
            <div>পেমেন্ট মাধ্যম: {{ $sale->paymentMethod->name ?? 'Cash' }}</div>
            <div>বিক্রেতা: {{ $sale->seller->name ?? 'Admin' }}</div>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-bordered align-middle mb-3 fs-7">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 5%;">#</th>
                <th class="text-start" style="width: 45%;">পণ্যের বিবরণ (Product)</th>
                <th style="width: 15%;">পরিমাণ</th>
                <th style="width: 15%;">দর (৳)</th>
                <th style="width: 20%;">মোট টাকা (৳)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->product->active_ingredient)
                            <div class="fs-8 text-muted">{{ $item->product->active_ingredient }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }} {{ $item->product->unit->short_name ?? '' }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="fw-bold fs-7">
            <tr>
                <td colspan="4" class="text-end">মোট সাবটোটাল:</td>
                <td class="text-end">{{ number_format($sale->total_amount, 2) }} ৳</td>
            </tr>
            @if($sale->discount_amount > 0)
            <tr>
                <td colspan="4" class="text-end text-danger">ডিসকাউন্ট ছাড়:</td>
                <td class="text-end text-danger">- {{ number_format($sale->discount_amount, 2) }} ৳</td>
            </tr>
            @endif
            <tr class="table-light fs-6">
                <td colspan="4" class="text-end">সর্বমোট (Net Payable):</td>
                <td class="text-end text-success">{{ number_format($sale->net_amount, 2) }} ৳</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end text-success">জমা / প্রদান (Paid):</td>
                <td class="text-end text-success">{{ number_format($sale->paid_amount, 2) }} ৳</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end text-danger">বকেয়া (Current Due):</td>
                <td class="text-end text-danger">{{ number_format($sale->due_amount, 2) }} ৳</td>
            </tr>
        </tfoot>
    </table>

    <div class="text-center my-3 fs-8 text-muted">
        *** কীটনাশক ও সার ব্যবহারের ক্ষেত্রে সঠিক মাত্রার নির্দেশিকা মেনে চলুন ***
    </div>

    <!-- Signatures -->
    <div class="row pt-5 mt-3 text-center fs-7">
        <div class="col-6">
            <div class="border-top pt-2">গ্রাহকের স্বাক্ষর</div>
        </div>
        <div class="col-6">
            <div class="border-top pt-2">বিক্রেতার স্বাক্ষর</div>
        </div>
    </div>
</div>

</body>
</html>
