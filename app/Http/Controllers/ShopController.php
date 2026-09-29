<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ShopController extends Controller
{
    public function index()
    {
        $items = Item::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->get();

        return view('shop.index', compact('items'));
    }

    public function customerShop()
    {
        $items = Item::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->get();

        return view('customer.shop.index', compact('items'));
    }
}
