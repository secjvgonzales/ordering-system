<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => abort(403),
        };
    }

    public function admin(Request $request)
    {
        $range = in_array($request->query('range'), ['today', '7days', '30days', 'all'], true)
            ? $request->query('range')
            : '30days';

        $startDate = match ($range) {
            'today' => now()->startOfDay(),
            '7days' => now()->subDays(6)->startOfDay(),
            '30days' => now()->subDays(29)->startOfDay(),
            default => null,
        };

        $orders = Order::query()->when($startDate, fn ($query) => $query->where('created_at', '>=', $startDate));
        $metrics = [
            'totalOrders' => (clone $orders)->count(),
            'pendingOrders' => (clone $orders)->where('status', 'pending')->count(),
            'processingOrders' => (clone $orders)->where('status', 'processing')->count(),
            'completedOrders' => (clone $orders)->where('status', 'completed')->count(),
            'cancelledOrders' => (clone $orders)->where('status', 'cancelled')->count(),
            'totalSales' => (clone $orders)->where('status', 'completed')->where('payment_status', 'paid')->sum('total_amount'),
            'totalProducts' => Item::count(),
            'activeProducts' => Item::where('status', 'active')->count(),
            'lowStockProducts' => Item::where('status', 'active')->where('stock_quantity', '<=', 5)->count(),
        ];

        $attentionOrders = Order::with('customer')
            ->whereIn('status', ['pending', 'confirmed', 'processing'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboards.admin', compact('range', 'metrics', 'attentionOrders'));
    }

    public function staff()
    {
        return view('dashboards.staff');
    }

    public function customer()
    {
        return view('dashboards.customer');
    }
}
