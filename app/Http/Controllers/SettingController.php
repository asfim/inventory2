<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name' => Setting::get('shop_name', 'সবুজ বাংলা এগ্রো মেডিসিন সেন্ট্রাল'),
            'shop_address' => Setting::get('shop_address', 'কৃষি মার্কেট, ধামরাই, ঢাকা-১৩৪০'),
            'shop_phone' => Setting::get('shop_phone', '01711-000000'),
            'shop_email' => Setting::get('shop_email', 'contact@agromed.com'),
            'currency_symbol' => Setting::get('currency_symbol', '৳'),
            'low_stock_threshold' => Setting::get('low_stock_threshold', '10'),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'nullable|string',
            'shop_phone' => 'nullable|string',
            'shop_email' => 'nullable|email',
            'currency_symbol' => 'required|string|max:10',
            'low_stock_threshold' => 'required|integer|min:1',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'সিস্টেম সেটিং সফলভাবে আপডেট করা হয়েছে!');
    }
}
