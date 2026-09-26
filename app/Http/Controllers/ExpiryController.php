<?php

namespace App\Http\Controllers;

use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpiryController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $batches = ProductBatch::with(['product.category', 'product.unit'])
            ->where('quantity', '>', 0)
            ->get();

        if ($filter === 'expired') {
            $batches = $batches->filter(fn($b) => $b->days_until_expiry < 0);
        } elseif ($filter === '30_days') {
            $batches = $batches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 30);
        } elseif ($filter === '60_days') {
            $batches = $batches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 60);
        } elseif ($filter === '90_days') {
            $batches = $batches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 90);
        }

        $allBatches = ProductBatch::where('quantity', '>', 0)->get();
        $expiredCount = $allBatches->filter(fn($b) => $b->days_until_expiry < 0)->count();
        $expiredValue = $allBatches->filter(fn($b) => $b->days_until_expiry < 0)->sum(fn($b) => $b->quantity * $b->purchase_price);

        $near30Count = $allBatches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 30)->count();
        $near60Count = $allBatches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 60)->count();
        $near90Count = $allBatches->filter(fn($b) => $b->days_until_expiry >= 0 && $b->days_until_expiry <= 90)->count();

        return view('expiry.index', compact(
            'batches',
            'filter',
            'expiredCount',
            'expiredValue',
            'near30Count',
            'near60Count',
            'near90Count'
        ));
    }
}
