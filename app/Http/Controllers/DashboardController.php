<?php

namespace App\Http\Controllers;

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

    public function admin()
    {
        return view('dashboards.admin');
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
