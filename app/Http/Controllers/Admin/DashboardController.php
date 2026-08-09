<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Quote;
use App\Models\Invoice;
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
            'quotes' => Quote::count(),
            'invoices' => Invoice::count(),
        ];
        
        // 1. Boutique (Orders) - Exclude cancelled orders
        $ordersQuery = Order::where('status', '!=', 'cancelled');
        $ordersCount = $ordersQuery->count();
        $ordersRevenue = $ordersQuery->sum('total_amount');
        
        $ordersCost = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->sum(\Illuminate\Support\Facades\DB::raw('order_items.quantity * order_items.purchase_price'));

        // 2. Factures (Invoices) - actual billing, count paid or sent status as revenue
        $invoicesQuery = Invoice::whereIn('status', ['paid', 'sent']);
        $invoicesCount = $invoicesQuery->count();
        $invoicesRevenue = $invoicesQuery->sum('total_amount');

        $invoicesCost = \Illuminate\Support\Facades\DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereIn('invoices.status', ['paid', 'sent'])
            ->sum(\Illuminate\Support\Facades\DB::raw('invoice_items.quantity * invoice_items.purchase_price'));

        // 3. Combined Financials & Reseller Commissions
        $ordersCommissions = \App\Models\PartnerCommission::where('status', 'paid')
            ->whereHas('order', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })->sum('commission_amount');

        $totalRevenue = $ordersRevenue + $invoicesRevenue - $ordersCommissions;
        $totalCost = $ordersCost + $invoicesCost;
        $totalProfit = $totalRevenue - $totalCost;
        $marginPercentage = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $financials = [
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'profit' => $totalProfit,
            'margin' => $marginPercentage,
            'commissions' => $ordersCommissions,
            'orders' => [
                'count' => $ordersCount,
                'revenue' => $ordersRevenue - $ordersCommissions,
                'gross_revenue' => $ordersRevenue,
                'cost' => $ordersCost,
                'profit' => ($ordersRevenue - $ordersCommissions) - $ordersCost,
            ],
            'invoices' => [
                'count' => $invoicesCount,
                'revenue' => $invoicesRevenue,
                'cost' => $invoicesCost,
                'profit' => $invoicesRevenue - $invoicesCost,
            ]
        ];

        // 4. Rolling 6-month historical calculations for Chart.js
        $months = [];
        $revenueData = [];
        $profitData = [];
        
        $frenchMonths = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthNum = $date->month;
            $yearNum = $date->year;
            
            $months[] = $frenchMonths[$monthNum] . ' ' . $yearNum;

            // Online checkout sales
            $ordersRev = Order::where('status', '!=', 'cancelled')
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $yearNum)
                ->sum('total_amount');

            // Commissions for this month
            $ordersComm = \App\Models\PartnerCommission::where('status', 'paid')
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $yearNum)
                ->sum('commission_amount');

            $ordersRev = $ordersRev - $ordersComm;

            $ordersCst = \Illuminate\Support\Facades\DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', '!=', 'cancelled')
                ->whereMonth('orders.created_at', $monthNum)
                ->whereYear('orders.created_at', $yearNum)
                ->sum(\Illuminate\Support\Facades\DB::raw('order_items.quantity * order_items.purchase_price'));

            // Manual invoices
            $invoicesRev = Invoice::whereIn('status', ['paid', 'sent'])
                ->whereMonth('created_at', $monthNum)
                ->whereYear('created_at', $yearNum)
                ->sum('total_amount');

            $invoicesCst = \Illuminate\Support\Facades\DB::table('invoice_items')
                ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                ->whereIn('invoices.status', ['paid', 'sent'])
                ->whereMonth('invoices.created_at', $monthNum)
                ->whereYear('invoices.created_at', $yearNum)
                ->sum(\Illuminate\Support\Facades\DB::raw('invoice_items.quantity * invoice_items.purchase_price'));

            // Calculations
            $monthlyRev = $ordersRev + $invoicesRev;
            $monthlyCst = $ordersCst + $invoicesCst;
            $monthlyPrf = $monthlyRev - $monthlyCst;

            $revenueData[] = $monthlyRev;
            $profitData[] = $monthlyPrf;
        }

        $chartData = [
            'labels' => $months,
            'revenue' => $revenueData,
            'profit' => $profitData,
        ];

        $latestContacts = Contact::latest()->take(5)->get();
        $latestOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'financials', 'chartData', 'latestContacts', 'latestOrders'));
    }
}
