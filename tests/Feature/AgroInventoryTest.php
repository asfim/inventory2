<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->role = Role::create([
        'name' => 'super_admin',
        'display_name' => 'Super Admin'
    ]);

    $this->user = User::create([
        'name' => 'Admin User',
        'email' => 'testadmin@agromed.com',
        'password' => bcrypt('password'),
        'role_id' => $this->role->id,
        'status' => true
    ]);

    $this->unit = Unit::create(['name' => 'Bottle', 'short_name' => 'Bott']);
    $this->cat = Category::create(['name' => 'Insecticide', 'code' => 'INS']);
    $this->pm = PaymentMethod::create(['name' => 'Cash', 'opening_balance' => 1000, 'current_balance' => 1000]);

    $this->product = Product::create([
        'name' => 'Vertimec 18 EC',
        'product_code' => 'P-TEST-01',
        'category_id' => $this->cat->id,
        'unit_id' => $this->unit->id,
        'purchase_price' => 300,
        'selling_price' => 400,
        'current_stock' => 50,
        'min_stock' => 10,
        'status' => true
    ]);

    ProductBatch::create([
        'product_id' => $this->product->id,
        'batch_number' => 'BATCH-TEST-01',
        'expiry_date' => now()->addDays(20)->toDateString(), // 20 days left
        'quantity' => 50,
        'purchase_price' => 300,
        'selling_price' => 400
    ]);

    $this->customer = Customer::create([
        'name' => 'Karim Farmer',
        'phone' => '01700000000',
        'current_due' => 0
    ]);

    $this->supplier = Supplier::create([
        'name' => 'Syngenta BD',
        'phone' => '01800000000',
        'current_due' => 0
    ]);
});

test('login page loads and super admin can login', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);

    $loginRes = $this->post('/login', [
        'email' => 'testadmin@agromed.com',
        'password' => 'password'
    ]);

    $loginRes->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($this->user);
});

test('dashboard page loads cleanly with summary cards', function () {
    $response = $this->actingAs($this->user)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Vertimec 18 EC');
});

test('pos page loads and can record a sale which reduces stock and updates customer ledger', function () {
    $response = $this->actingAs($this->user)->get('/pos');
    $response->assertStatus(200);

    $saleRes = $this->actingAs($this->user)->postJson('/sales', [
        'invoice_no' => 'INV-TEST-001',
        'sale_date' => now()->toDateString(),
        'customer_id' => $this->customer->id,
        'payment_method_id' => $this->pm->id,
        'total_amount' => 800,
        'discount_amount' => 0,
        'paid_amount' => 500,
        'items' => [
            [
                'product_id' => $this->product->id,
                'batch_number' => 'BATCH-TEST-01',
                'quantity' => 2,
                'unit_price' => 400
            ]
        ]
    ]);

    $saleRes->assertStatus(200);
    $saleRes->assertJson(['success' => true]);

    // Verify stock decreased from 50 to 48
    expect($this->product->fresh()->current_stock)->toBe(48);

    // Verify customer due updated to 300 (800 - 500)
    expect((float) $this->customer->fresh()->current_due)->toBe(300.0);
});

test('purchase store increases stock and updates supplier due', function () {
    $purchaseRes = $this->actingAs($this->user)->post('/purchases', [
        'invoice_no' => 'PUR-TEST-001',
        'purchase_date' => now()->toDateString(),
        'supplier_id' => $this->supplier->id,
        'payment_method_id' => $this->pm->id,
        'total_amount' => 3000,
        'paid_amount' => 1000,
        'items' => [
            [
                'product_id' => $this->product->id,
                'batch_number' => 'BATCH-TEST-02',
                'expiry_date' => now()->addYear()->toDateString(),
                'quantity' => 10,
                'unit_price' => 300,
                'selling_price' => 400
            ]
        ]
    ]);

    $purchaseRes->assertRedirect();

    // Verify stock increased from 50 to 60
    expect($this->product->fresh()->current_stock)->toBe(60);

    // Verify supplier due updated to 2000 (3000 - 1000)
    expect((float) $this->supplier->fresh()->current_due)->toBe(2000.0);
});

test('expiry management displays batch expiring soon', function () {
    $response = $this->actingAs($this->user)->get('/expiry?filter=30_days');
    $response->assertStatus(200);
    $response->assertSee('BATCH-TEST-01');
});
