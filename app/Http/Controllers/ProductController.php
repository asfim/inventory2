<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Unit;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'unit']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('active_ingredient', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(15);
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();
        $units = Unit::where('status', true)->get();

        return view('products.index', compact('products', 'categories', 'brands', 'units'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();
        $units = Unit::where('status', true)->get();
        return view('products.create', compact('categories', 'brands', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products',
            'sku' => 'nullable|string|max:100|unique:products',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'active_ingredient' => 'nullable|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'current_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'batch_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'product_code' => $validated['product_code'],
            'sku' => $validated['sku'] ?? null,
            'barcode' => $validated['barcode'] ?? null,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'] ?? null,
            'active_ingredient' => $validated['active_ingredient'] ?? null,
            'unit_id' => $validated['unit_id'],
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'wholesale_price' => $validated['wholesale_price'] ?? $validated['selling_price'],
            'min_stock' => $validated['min_stock'],
            'max_stock' => $validated['max_stock'] ?? 1000,
            'current_stock' => $validated['current_stock'] ?? 0,
            'description' => $validated['description'] ?? null,
            'status' => true,
        ]);

        if (!empty($validated['batch_number']) && !empty($validated['expiry_date']) && ($validated['current_stock'] ?? 0) > 0) {
            ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => $validated['batch_number'],
                'expiry_date' => $validated['expiry_date'],
                'quantity' => $validated['current_stock'],
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
            ]);
        }

        AuditLogService::log('create_product', Product::class, $product->id, "পণ্য তৈরি করা হয়েছে: {$product->name}");

        return redirect()->route('products.index')->with('success', 'পণ্য সফলভাবে তৈরি করা হয়েছে! (Product created successfully)');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'unit', 'batches']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();
        $units = Unit::where('status', true)->get();
        return view('products.edit', compact('product', 'categories', 'brands', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:100|unique:products,product_code,' . $product->id,
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'active_ingredient' => 'nullable|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $product->update($validated);

        AuditLogService::log('update_product', Product::class, $product->id, "পণ্য তথ্য আপডেট করা হয়েছে: {$product->name}");

        return redirect()->route('products.index')->with('success', 'পণ্য তথ্য সফলভাবে আপডেট করা হয়েছে!');
    }

    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->delete();

        AuditLogService::log('delete_product', Product::class, $product->id, "পণ্য মুছে ফেলা হয়েছে: {$productName}");

        return redirect()->route('products.index')->with('success', 'পণ্য মুছে ফেলা হয়েছে!');
    }

    public function searchApi(Request $request)
    {
        $term = $request->get('term', '');
        $products = Product::with(['unit', 'batches'])
            ->where('status', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('product_code', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%")
                  ->orWhere('active_ingredient', 'like', "%{$term}%");
            })
            ->take(15)
            ->get();

        return response()->json($products);
    }
}
