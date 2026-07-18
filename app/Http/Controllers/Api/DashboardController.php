<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\Warranty;
use App\Models\MaintenanceContract;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'client') {
            return $this->clientDashboard($user);
        } elseif ($user->role === 'technicien') {
            return $this->technicianDashboard($user);
        } else {
            return $this->staffDashboard($user);
        }
    }

    private function clientDashboard($user)
    {
        $client = $user->client;
        $clientId = $client ? $client->id : null;

        $totalSpent = 0.00;
        $activeWarranties = 0;
        $openTickets = 0;
        $walletBalance = 0.00;

        if ($clientId) {
            $totalSpent = Order::where('client_id', $clientId)
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $activeWarranties = Warranty::where('client_id', $clientId)
                ->where('status', 'active')
                ->count();

            $openTickets = Ticket::where('client_id', $clientId)
                ->whereIn('status', ['open', 'diagnosed', 'in_progress', 'waiting_parts'])
                ->count();

            $walletBalance = $client->wallet_balance;
        } else {
            $openTickets = Ticket::where('client_email', $user->email)
                ->whereIn('status', ['open', 'diagnosed', 'in_progress', 'waiting_parts'])
                ->count();
        }

        // Recent orders
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'number' => $order->order_number ?? 'CMD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'total' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('d/m/Y'),
                ];
            });

        return response()->json([
            'role' => 'client',
            'kpis' => [
                'total_spent' => $totalSpent,
                'active_warranties' => $activeWarranties,
                'open_tickets' => $openTickets,
                'wallet_balance' => $walletBalance,
            ],
            'recent_orders' => $recentOrders
        ]);
    }

    private function technicianDashboard($user)
    {
        $assignedTickets = Ticket::where('assigned_to', $user->id)
            ->whereIn('status', ['open', 'diagnosed', 'in_progress', 'waiting_parts'])
            ->count();

        $urgentTickets = Ticket::where('assigned_to', $user->id)
            ->where('priority', 'urgent')
            ->whereIn('status', ['open', 'diagnosed', 'in_progress', 'waiting_parts'])
            ->count();

        $resolvedToday = Ticket::where('assigned_to', $user->id)
            ->where('status', 'resolved')
            ->whereDate('resolved_at', now()->toDateString())
            ->count();

        $scheduledToday = Ticket::where('assigned_to', $user->id)
            ->whereDate('scheduled_date', now()->toDateString())
            ->count();

        // Urgent tickets list
        $urgentList = Ticket::where('assigned_to', $user->id)
            ->where('priority', 'urgent')
            ->whereIn('status', ['open', 'diagnosed', 'in_progress', 'waiting_parts'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'number' => $t->number,
                    'title' => $t->title,
                    'status' => $t->status,
                    'client_name' => $t->client_name,
                ];
            });

        return response()->json([
            'role' => 'technicien',
            'kpis' => [
                'assigned_tickets' => $assignedTickets,
                'urgent_tickets' => $urgentTickets,
                'resolved_today' => $resolvedToday,
                'scheduled_today' => $scheduledToday,
            ],
            'urgent_tickets_list' => $urgentList
        ]);
    }

    private function staffDashboard($user)
    {
        $totalClients = Client::count();
        $activeContracts = MaintenanceContract::where('status', 'active')->count();
        
        $todaySales = Order::whereDate('created_at', now()->toDateString())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOpenTickets = Ticket::whereIn('status', ['open', 'diagnosed', 'in_progress', 'waiting_parts'])->count();
        
        $lowStockProducts = Product::where('stock', '<=', 5)->count();

        return response()->json([
            'role' => $user->role,
            'kpis' => [
                'total_clients' => $totalClients,
                'active_contracts' => $activeContracts,
                'today_sales' => $todaySales,
                'total_open_tickets' => $totalOpenTickets,
                'low_stock_products' => $lowStockProducts,
            ]
        ]);
    }
}
