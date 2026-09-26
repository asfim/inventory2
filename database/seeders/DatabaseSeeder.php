<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], [
            'display_name' => 'Super Admin',
            'description' => 'Full System Control',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Admin',
            'description' => 'Administrator Access',
        ]);

        $managerRole = Role::firstOrCreate(['name' => 'manager'], [
            'display_name' => 'Manager',
            'description' => 'Store & Sales Management',
        ]);

        $salesmanRole = Role::firstOrCreate(['name' => 'salesman'], [
            'display_name' => 'Salesman',
            'description' => 'POS & Counter Sales',
        ]);

        $permissions = [
            ['name' => 'view_dashboard', 'group_name' => 'Dashboard', 'display_name' => 'View Dashboard'],
            ['name' => 'manage_purchases', 'group_name' => 'Purchases', 'display_name' => 'Manage Purchases'],
            ['name' => 'manage_sales', 'group_name' => 'Sales', 'display_name' => 'Manage Sales & POS'],
            ['name' => 'manage_stock', 'group_name' => 'Stock', 'display_name' => 'Manage Stock & Expiry'],
            ['name' => 'manage_cashbook', 'group_name' => 'Cashbook', 'display_name' => 'Manage Cashbook & Ledgers'],
            ['name' => 'manage_expenses', 'group_name' => 'Expenses', 'display_name' => 'Manage Expenses'],
            ['name' => 'manage_investments', 'group_name' => 'Investments', 'display_name' => 'Manage Investments'],
            ['name' => 'manage_products', 'group_name' => 'Products', 'display_name' => 'Manage Products & Master Data'],
            ['name' => 'manage_suppliers', 'group_name' => 'Suppliers', 'display_name' => 'Manage Suppliers'],
            ['name' => 'manage_customers', 'group_name' => 'Customers', 'display_name' => 'Manage Customers'],
            ['name' => 'manage_employees', 'group_name' => 'Employees', 'display_name' => 'Manage Employees'],
            ['name' => 'view_reports', 'group_name' => 'Reports', 'display_name' => 'View Reports'],
            ['name' => 'manage_settings', 'group_name' => 'Settings', 'display_name' => 'Manage Settings & Users'],
        ];

        foreach ($permissions as $p) {
            $perm = Permission::firstOrCreate(['name' => $p['name']], $p);
            $superAdminRole->permissions()->syncWithoutDetaching([$perm->id]);
            $adminRole->permissions()->syncWithoutDetaching([$perm->id]);
        }

        // Create Super Admin User
        $user = User::firstOrCreate(['email' => 'admin@agromed.com'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'phone' => '01711-000000',
            'status' => true,
        ]);

        // 2. Categories
        $cat1 = Category::firstOrCreate(['code' => 'INS'], ['name' => 'কীটনাশক (Insecticide)', 'description' => 'পোকা-মাকড় দমনের ঔষধ']);
        $cat2 = Category::firstOrCreate(['code' => 'FUN'], ['name' => 'ছত্রাকনাশক (Fungicide)', 'description' => 'রোগ ও ছত্রাক দমনের ঔষধ']);
        $cat3 = Category::firstOrCreate(['code' => 'HER'], ['name' => 'আগাছানাশক (Herbicide)', 'description' => 'ক্ষতিকর আগাছা বিনাশক']);
        $cat4 = Category::firstOrCreate(['code' => 'FER'], ['name' => 'রাসায়নিক সার (Fertilizer)', 'description' => 'ইউরিয়া, টিএসপি, ডিএপি ও এমপি সার']);
        $cat5 = Category::firstOrCreate(['code' => 'SED'], ['name' => 'উন্নত বীজ (Seed)', 'description' => 'ধান, গম, ভুট্টা ও সবজির বীজ']);
        $cat6 = Category::firstOrCreate(['code' => 'PGR'], ['name' => 'উদ্ভিদ বৃদ্ধি নিয়ন্ত্রক (PGR)', 'description' => 'ফসল ও ফল বৃদ্ধির জন্য ভিটামিন/হরমোন']);

        // 3. Brands
        $brand1 = Brand::firstOrCreate(['name' => 'Syngenta Bangladesh'], ['description' => 'বহুজাতিক কৃষি ঔষুধ কোম্পানি']);
        $brand2 = Brand::firstOrCreate(['name' => 'Bayer CropScience'], ['description' => 'জার্মান এগ্রো কেমিক্যাল কোম্পানি']);
        $brand3 = Brand::firstOrCreate(['name' => 'Auto Crop Care'], ['description' => 'স্বনামধন্য দেশীয় কীটনাশক ব্র্যান্ড']);
        $brand4 = Brand::firstOrCreate(['name' => 'ACI Formulations'], ['description' => 'এসিআই এগ্রো বিজনেস']);
        $brand5 = Brand::firstOrCreate(['name' => 'National Agricare'], ['description' => 'কৃষি ঔষধ প্রস্তুতকারক']);

        // 4. Units
        $unitBott = Unit::firstOrCreate(['short_name' => 'Bottle'], ['name' => 'বোতল (Bottle)']);
        $unitPack = Unit::firstOrCreate(['short_name' => 'Pkt'], ['name' => 'প্যাকেট (Packet)']);
        $unitKg = Unit::firstOrCreate(['short_name' => 'Kg'], ['name' => 'কেজি (Kg)']);
        $unitBag = Unit::firstOrCreate(['short_name' => 'Bag'], ['name' => 'বস্তা (Bag)']);
        $unitLitr = Unit::firstOrCreate(['short_name' => 'Ltr'], ['name' => 'লিটার (Liter)']);

        // 5. Payment Methods
        $pmCash = PaymentMethod::firstOrCreate(['name' => 'Cash (ক্যাশ)'], ['account_number' => 'N/A', 'opening_balance' => 80000, 'current_balance' => 85400]);
        $pmBank = PaymentMethod::firstOrCreate(['name' => 'Bank (ডাচ-বাংলা ব্যাংক)'], ['account_number' => '102-110-45892', 'account_holder' => 'সবুজ বাংলা এগ্রো', 'opening_balance' => 150000, 'current_balance' => 150000]);
        $pmBkash = PaymentMethod::firstOrCreate(['name' => 'bKash (বিকাশ মার্চেন্ট)'], ['account_number' => '01711-000000', 'account_holder' => 'সবুজ বাংলা এগ্রো', 'opening_balance' => 50000, 'current_balance' => 52100]);
        $pmNagad = PaymentMethod::firstOrCreate(['name' => 'Nagad (নগদ মার্চেন্ট)'], ['account_number' => '01800-000000', 'account_holder' => 'সবুজ বাংলা এগ্রো', 'opening_balance' => 30000, 'current_balance' => 30000]);

        // 6. Expense Categories
        ExpenseCategory::firstOrCreate(['name' => 'Shop Rent (দোকান ভাড়া)']);
        ExpenseCategory::firstOrCreate(['name' => 'Electricity (বিদ্যুৎ বিল)']);
        ExpenseCategory::firstOrCreate(['name' => 'Internet (ইন্টারনেট বিল)']);
        ExpenseCategory::firstOrCreate(['name' => 'Transport (পরিবহন খরচ)']);
        $expCatSal = ExpenseCategory::firstOrCreate(['name' => 'Salary (কর্মচারী বেতন)']);
        ExpenseCategory::firstOrCreate(['name' => 'Office Expense (অফিস খরচ)']);

        // 7. Settings
        Setting::set('shop_name', 'সবুজ বাংলা এগ্রো মেডিসিন সেন্ট্রাল');
        Setting::set('shop_address', 'কৃষি মার্কেট, ধামরাই, ঢাকা-১৩৪০');
        Setting::set('shop_phone', '01711-000000, 01800-112233');
        Setting::set('shop_email', 'contact@agromed.com');
        Setting::set('currency_symbol', '৳');
        Setting::set('low_stock_threshold', '10');

        // 8. Branches
        Branch::firstOrCreate(['code' => 'BR-01'], ['name' => 'প্রধান শাখা (ধামরাই)', 'phone' => '01711-000000', 'address' => 'ধামরাই, ঢাকা', 'is_main' => true]);
        Branch::firstOrCreate(['code' => 'BR-02'], ['name' => 'সাভার ব্রাঞ্চ', 'phone' => '01800-112233', 'address' => 'সাভার বাসস্ট্যান্ড', 'is_main' => false]);

        // 9. Suppliers
        $sup1 = Supplier::firstOrCreate(['phone' => '01700-111222'], [
            'name' => 'সিনজেন্টা বাংলাদেশ লিমিটেড',
            'company_name' => 'Syngenta Bangladesh Ltd.',
            'email' => 'sales@syngenta.com.bd',
            'address' => 'তেজগাঁও শিল্প এলাকা, ঢাকা',
            'opening_due' => 0,
            'current_due' => 15000,
        ]);

        $sup2 = Supplier::firstOrCreate(['phone' => '01800-333444'], [
            'name' => 'অটো ক্রপ কেয়ার লিমিটেড',
            'company_name' => 'Auto Crop Care Ltd.',
            'email' => 'info@autocropcare.com',
            'address' => 'মহাখালী, ঢাকা',
            'opening_due' => 0,
            'current_due' => 8000,
        ]);

        $sup3 = Supplier::firstOrCreate(['phone' => '01900-555666'], [
            'name' => 'এসিআই ফরমুলেশনস লিমিটেড',
            'company_name' => 'ACI Formulations Ltd.',
            'email' => 'agri@aci-bd.com',
            'address' => 'মতিঝিল, ঢাকা',
            'opening_due' => 0,
            'current_due' => 0,
        ]);

        // 10. Customers
        $cust1 = Customer::firstOrCreate(['phone' => '01712-345678'], [
            'name' => 'আলহাজ্ব রফিকুল ইসলাম (কৃষক)',
            'address' => 'গ্রাম: কালামপুর, ধামরাই, ঢাকা',
            'opening_due' => 0,
            'current_due' => 3500,
        ]);

        $cust2 = Customer::firstOrCreate(['phone' => '01819-876543'], [
            'name' => 'মোঃ আব্দুল কুদ্দুস (সবজি চাষী)',
            'address' => 'গ্রাম: হেমায়েতপুর, সাভার, ঢাকা',
            'opening_due' => 0,
            'current_due' => 1200,
        ]);

        $cust3 = Customer::firstOrCreate(['phone' => '01911-223344'], [
            'name' => 'হাজী মোঃ ইউসুফ আলী (ফল বাগান)',
            'address' => 'গ্রাম: বাড়াবাড়িয়া, ধামরাই, ঢাকা',
            'opening_due' => 0,
            'current_due' => 0,
        ]);

        // 11. Employees
        $emp1 = Employee::firstOrCreate(['email' => 'tarek@agromed.com'], [
            'name' => 'মোঃ তারেক রহমান',
            'phone' => '01755-998877',
            'address' => 'ধামরাই, ঢাকা',
            'designation' => 'Sales Manager',
            'joining_date' => '2025-01-10',
            'salary' => 20000,
        ]);

        // 12. Products & Batches
        $p1 = Product::firstOrCreate(['product_code' => 'P-INS-001'], [
            'name' => 'ভার্টিমেক ১৮ ইসি (Vertimec 18 EC)',
            'sku' => 'VERT-100',
            'barcode' => '8934521001',
            'category_id' => $cat1->id,
            'brand_id' => $brand1->id,
            'active_ingredient' => 'অ্যাবামেকটিন ১.৮% (Abamectin 1.8% EC)',
            'unit_id' => $unitBott->id,
            'purchase_price' => 320,
            'selling_price' => 380,
            'wholesale_price' => 360,
            'min_stock' => 10,
            'max_stock' => 500,
            'current_stock' => 45,
            'description' => 'ধান ও সবজির মাকড় ও পাতা মোড়ানো পোকা দমনে অত্যন্ত কার্যকর।',
        ]);

        ProductBatch::firstOrCreate(['batch_number' => 'SYN-2026-A1'], [
            'product_id' => $p1->id,
            'expiry_date' => '2027-10-30',
            'quantity' => 45,
            'purchase_price' => 320,
            'selling_price' => 380,
        ]);

        $p2 = Product::firstOrCreate(['product_code' => 'P-FUN-002'], [
            'name' => 'স্কোর ২৫০ ইসি (Score 250 EC)',
            'sku' => 'SCORE-100',
            'barcode' => '8934521002',
            'category_id' => $cat2->id,
            'brand_id' => $brand1->id,
            'active_ingredient' => 'ডাইফেনোকোনাজল ২৫০ গ্রাম/লিটার (Difenoconazole)',
            'unit_id' => $unitBott->id,
            'purchase_price' => 450,
            'selling_price' => 520,
            'wholesale_price' => 490,
            'min_stock' => 10,
            'max_stock' => 300,
            'current_stock' => 20,
            'description' => 'ধানের ব্লাস্ট ও আলুর মড়ক রোগে কার্যকারী পচননাশক।',
        ]);

        ProductBatch::firstOrCreate(['batch_number' => 'SYN-2026-B2'], [
            'product_id' => $p2->id,
            'expiry_date' => '2026-10-25', // Expiring in ~30 days!
            'quantity' => 20,
            'purchase_price' => 450,
            'selling_price' => 520,
        ]);

        $p3 = Product::firstOrCreate(['product_code' => 'P-FUN-003'], [
            'name' => 'অটোস্টিন ৫০ ডব্লিউডিজি (Autostin 50 WDG)',
            'sku' => 'AUTOST-100',
            'barcode' => '8934521003',
            'category_id' => $cat2->id,
            'brand_id' => $brand3->id,
            'active_ingredient' => 'কারবেনডাজিম ৫০% (Carbendazim 50%)',
            'unit_id' => $unitPack->id,
            'purchase_price' => 110,
            'selling_price' => 140,
            'wholesale_price' => 130,
            'min_stock' => 15,
            'max_stock' => 400,
            'current_stock' => 60,
            'description' => 'সর্বপ্রকার সবজি ও শস্যের পচন ও ছত্রাক রোধক।',
        ]);

        ProductBatch::firstOrCreate(['batch_number' => 'AUT-2026-C3'], [
            'product_id' => $p3->id,
            'expiry_date' => '2027-06-15',
            'quantity' => 60,
            'purchase_price' => 110,
            'selling_price' => 140,
        ]);

        $p4 = Product::firstOrCreate(['product_code' => 'P-HER-004'], [
            'name' => 'রাউন্ডআপ ৪৮০ এসএল (Roundup 480 SL)',
            'sku' => 'ROUND-500',
            'barcode' => '8934521004',
            'category_id' => $cat3->id,
            'brand_id' => $brand2->id,
            'active_ingredient' => 'গ্লাইফোসেট ৪৮% (Glyphosate 48% SL)',
            'unit_id' => $unitBott->id,
            'purchase_price' => 580,
            'selling_price' => 680,
            'wholesale_price' => 650,
            'min_stock' => 8,
            'max_stock' => 200,
            'current_stock' => 5, // LOW STOCK ALERT!
            'description' => 'জমি প্রস্তুতকালে স্থায়ী আগাছা দমনে অপ্রতিদ্বন্দ্বী।',
        ]);

        ProductBatch::firstOrCreate(['batch_number' => 'BAY-2025-EX'], [
            'product_id' => $p4->id,
            'expiry_date' => '2026-08-01', // EXPIRED ALERT!
            'quantity' => 5,
            'purchase_price' => 580,
            'selling_price' => 680,
        ]);

        $p5 = Product::firstOrCreate(['product_code' => 'P-PGR-005'], [
            'name' => 'ফ্লোরা প্ল্যান্ট বুস্টার (Flora Plant Booster)',
            'sku' => 'FLORA-500',
            'barcode' => '8934521005',
            'category_id' => $cat6->id,
            'brand_id' => $brand4->id,
            'active_ingredient' => 'নাইট্রোবেনজিন ২০% (Nitrobenzene 20% w/w)',
            'unit_id' => $unitBott->id,
            'purchase_price' => 380,
            'selling_price' => 460,
            'wholesale_price' => 430,
            'min_stock' => 10,
            'max_stock' => 300,
            'current_stock' => 35,
            'description' => 'ফুলের সংখ্যা বৃদ্ধি ও ফল ঝরে পড়া রোধের জন্য টনিক।',
        ]);

        ProductBatch::firstOrCreate(['batch_number' => 'ACI-2026-D4'], [
            'product_id' => $p5->id,
            'expiry_date' => '2027-11-20',
            'quantity' => 35,
            'purchase_price' => 380,
            'selling_price' => 460,
        ]);

        // 13. Purchases
        $pur1 = Purchase::firstOrCreate(['invoice_no' => 'PUR-202609-001'], [
            'purchase_date' => '2026-09-20',
            'supplier_id' => $sup1->id,
            'total_amount' => 20000,
            'discount_amount' => 1000,
            'tax_amount' => 0,
            'net_amount' => 19000,
            'paid_amount' => 4000,
            'due_amount' => 15000,
            'payment_method_id' => $pmCash->id,
            'payment_status' => 'partial',
            'notes' => 'স্টক বৃদ্ধির উদ্দেশ্যে সিনজেন্টা ঔষধ ক্রয়',
            'created_by' => $user->id,
        ]);

        PurchaseItem::firstOrCreate([
            'purchase_id' => $pur1->id,
            'product_id' => $p1->id,
            'batch_number' => 'SYN-2026-A1',
        ], [
            'expiry_date' => '2027-10-30',
            'quantity' => 50,
            'unit_price' => 320,
            'discount' => 0,
            'tax' => 0,
            'subtotal' => 16000,
        ]);

        SupplierLedger::firstOrCreate([
            'supplier_id' => $sup1->id,
            'reference' => 'PUR-202609-001',
        ], [
            'date' => '2026-09-20',
            'type' => 'purchase',
            'debit' => 4000,
            'credit' => 19000,
            'balance' => 15000,
            'note' => 'চালান নং PUR-202609-001 ক্রয়',
            'created_by' => $user->id,
        ]);

        // 14. Demo Sales & Customer Ledger
        $sale1 = Sale::firstOrCreate(['invoice_no' => 'INV-202609-001'], [
            'sale_date' => date('Y-m-d'),
            'customer_id' => $cust1->id,
            'total_amount' => 5400,
            'discount_amount' => 200,
            'tax_amount' => 0,
            'net_amount' => 5200,
            'paid_amount' => 1700,
            'due_amount' => 3500,
            'change_amount' => 0,
            'payment_method_id' => $pmCash->id,
            'payment_status' => 'partial',
            'seller_id' => $user->id,
            'notes' => 'ধানের জমির জন্য বালাইনাশক বিক্রি',
        ]);

        SaleItem::firstOrCreate([
            'sale_id' => $sale1->id,
            'product_id' => $p1->id,
        ], [
            'batch_number' => 'SYN-2026-A1',
            'quantity' => 5,
            'unit_price' => 380,
            'discount' => 0,
            'subtotal' => 1900,
        ]);

        SaleItem::firstOrCreate([
            'sale_id' => $sale1->id,
            'product_id' => $p2->id,
        ], [
            'batch_number' => 'SYN-2026-B2',
            'quantity' => 5,
            'unit_price' => 520,
            'discount' => 0,
            'subtotal' => 2600,
        ]);

        CustomerLedger::firstOrCreate([
            'customer_id' => $cust1->id,
            'reference' => 'INV-202609-001',
        ], [
            'date' => date('Y-m-d'),
            'type' => 'sale',
            'debit' => 5200,
            'credit' => 1700,
            'balance' => 3500,
            'note' => 'ইনভয়েস INV-202609-001 সেলস',
            'created_by' => $user->id,
        ]);

        // 15. Demo Investment
        Investment::firstOrCreate(['reference' => 'INV-CAP-01'], [
            'investor_name' => 'হাজী মোঃ নুরুল ইসলাম (মালিক)',
            'investment_date' => '2026-09-01',
            'amount' => 100000,
            'payment_method_id' => $pmCash->id,
            'note' => 'দোকানের প্রাথমিক কার্যকরী মূলধন বিনিয়োগ',
            'created_by' => $user->id,
        ]);

        // 16. Demo Expense
        Expense::firstOrCreate([
            'description' => 'কর্মচারী তারেক রহমানের চলতি মাসের বেতন',
        ], [
            'expense_date' => date('Y-m-d'),
            'expense_category_id' => $expCatSal->id,
            'amount' => 15000,
            'payment_method_id' => $pmCash->id,
            'created_by' => $user->id,
        ]);
    }
}
