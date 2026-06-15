<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        
        return view('admin.dashboard', compact('totalProducts', 'recentOrders', 'totalRevenue'));
    }
}
