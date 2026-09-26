<?php

use App\Models\Setting;

if (!function_exists('lang')) {
    function lang(string $bnText, string $enText): string
    {
        $locale = session('locale', 'bn');
        return $locale === 'en' ? $enText : $bnText;
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount): string
    {
        $symbol = Setting::get('currency_symbol', '৳');
        return $symbol . ' ' . number_format((float) $amount, 2);
    }
}

if (!function_exists('active_nav')) {
    function active_nav($routeNames): string
    {
        if (is_array($routeNames)) {
            foreach ($routeNames as $r) {
                if (request()->routeIs($r)) return 'active';
            }
            return '';
        }
        return request()->routeIs($routeNames) ? 'active' : '';
    }
}
