<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        [$items, $categories] = $this->products($request);

        return view('shop.index', compact('items', 'categories'));
    }

    public function customerShop(Request $request)
    {
        [$items, $categories] = $this->products($request);

        return view('customer.shop.index', compact('items', 'categories'));
    }

    public function search(Request $request): JsonResponse
    {
        [$items] = $this->products($request);

        return response()->json([
            'html' => view('shop.partials.product-grid', compact('items'))->render(),
            'count' => $items->count(),
        ]);
    }

    public function show(Item $item)
    {
        abort_unless($item->status === 'active', 404);

        return view('shop.show', compact('item'));
    }

    private function products(Request $request): array
    {
        $search = trim($request->string('q')->toString());
        $category = trim($request->string('category')->toString());
        $items = Item::query()
            ->where('status', 'active')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->latest()
            ->get();

        $categories = collect(Item::CATEGORIES);

        return [$items, $categories];
    }
}
