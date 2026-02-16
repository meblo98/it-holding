<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'services' => Service::count(),
            'projects' => Project::count(),
            'posts' => Post::count(),
            'products' => Product::count(),
            'orders' => Order::count(),
            'contacts' => Contact::count(),
        ];
        
        $latestContacts = Contact::latest()->take(5)->get();
        $latestOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestContacts', 'latestOrders'));
    }
}
