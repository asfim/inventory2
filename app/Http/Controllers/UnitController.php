<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('products')->orderBy('id', 'desc')->get();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
        ]);

        Unit::create([
            'name' => $validated['name'],
            'short_name' => $validated['short_name'],
            'status' => true,
        ]);

        return back()->with('success', 'ইউনিট সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'status' => 'required|boolean',
        ]);

        $unit->update($validated);
        return back()->with('success', 'ইউনিট আপডেট করা হয়েছে!');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->count() > 0) {
            return back()->with('error', 'এই ইউনিটে পণ্য থাকায় মুছে ফেলা সম্ভব নয়!');
        }
        $unit->delete();
        return back()->with('success', 'ইউনিট মুছে ফেলা হয়েছে!');
    }
}
