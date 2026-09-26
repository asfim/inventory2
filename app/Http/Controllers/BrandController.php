<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('id', 'desc')->get();
        return view('brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Brand::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => true,
        ]);

        return back()->with('success', 'ব্র্যান্ড সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $brand->update($validated);
        return back()->with('success', 'ব্র্যান্ড আপডেট করা হয়েছে!');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'এই ব্র্যান্ডে পণ্য থাকায় মুছে ফেলা সম্ভব নয়!');
        }
        $brand->delete();
        return back()->with('success', 'ব্র্যান্ড মুছে ফেলা হয়েছে!');
    }
}
