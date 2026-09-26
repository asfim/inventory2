<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('id', 'desc')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? strtoupper(substr($validated['name'], 0, 3)),
            'description' => $validated['description'] ?? null,
            'status' => true,
        ]);

        return back()->with('success', 'ক্যাটাগরি সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $category->update($validated);
        return back()->with('success', 'ক্যাটাগরি আপডেট করা হয়েছে!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'এই ক্যাটাগরিতে পণ্য থাকায় মুছে ফেলা সম্ভব নয়!');
        }
        $category->delete();
        return back()->with('success', 'ক্যাটাগরি মুছে ফেলা হয়েছে!');
    }
}
