<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warranty;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Overview of basic KPIs
        $salesCount = Order::count();
        $totalCA = Order::where('payment_status', 'paid')->sum('total_amount');
        
        $stockValuation = Product::sum(DB::raw('stock * price'));
        $purchaseValuation = Product::sum(DB::raw('stock * purchase_price'));
        
        $openTicketsCount = Ticket::whereNotIn('status', ['resolved', 'closed'])->count();
        $activeWarranties = Warranty::where('status', 'active')->count();

        return view('admin.reports.index', compact(
            'salesCount', 'totalCA', 'stockValuation', 'purchaseValuation', 'openTicketsCount', 'activeWarranties'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $productId = $request->input('product_id');
        $clientId = $request->input('client_id');

        $query = OrderItem::with(['order', 'product'])
            ->whereHas('order', function ($q) use ($startDate, $endDate, $clientId) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($clientId) {
                    $q->where('client_id', $clientId);
                }
            });

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $items = $query->latest()->get();

        $totalSales = $items->sum(fn($i) => $i->quantity * $i->price);
        $totalQty = $items->sum('quantity');

        $products = Product::orderBy('name')->get();
        $clients = Client::orderBy('company_name')->orderBy('last_name')->get();

        return view('admin.reports.sales', compact('items', 'totalSales', 'totalQty', 'products', 'clients', 'startDate', 'endDate'));
    }

    public function stocks()
    {
        $products = Product::orderBy('stock')->get();
        
        $totalStock = $products->sum('stock');
        $totalValuation = $products->sum(fn($p) => $p->stock * $p->price);
        $totalPurchaseValuation = $products->sum(fn($p) => $p->stock * $p->purchase_price);

        // Low stock threshold
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock')->get();

        return view('admin.reports.stocks', compact('products', 'totalStock', 'totalValuation', 'totalPurchaseValuation', 'lowStockProducts'));
    }

    public function profits(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $items = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->where('payment_status', 'paid')
              ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })->get();

        $revenue = $items->sum(fn($i) => $i->quantity * $i->price);
        $cogs = $items->sum(fn($i) => $i->quantity * ($i->purchase_price ?: 0));
        $grossProfit = $revenue - $cogs;
        $margin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        return view('admin.reports.profits', compact('revenue', 'cogs', 'grossProfit', 'margin', 'startDate', 'endDate'));
    }

    public function suppliers()
    {
        $suppliers = Supplier::withCount('products')->get();
        
        // Sum total stock value supplied per supplier
        $supplierData = [];
        foreach ($suppliers as $supplier) {
            $products = Product::where('brand_id', $supplier->id)->get(); // Assuming Brand relates to Supplier, or Supplier has products relation
            $stockVal = $products->sum(fn($p) => $p->stock * $p->price);
            $supplierData[] = [
                'supplier' => $supplier,
                'stock_value' => $stockVal,
                'products_count' => $products->count()
            ];
        }

        return view('admin.reports.suppliers', compact('supplierData'));
    }

    public function sav()
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::whereIn('status', ['new', 'open', 'pending'])->count();
        $resolvedTickets = Ticket::where('status', 'resolved')->count();
        
        $tickets = Ticket::with('client')->latest()->take(10)->get();
        
        $activeWarranties = Warranty::where('status', 'active')->count();
        $expiredWarranties = Warranty::where('status', 'expired')->count();

        return view('admin.reports.sav', compact('totalTickets', 'openTickets', 'resolvedTickets', 'tickets', 'activeWarranties', 'expiredWarranties'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'sales');
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=report_{$type}_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($type, $request) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'sales') {
                fputcsv($file, ['ID Facture', 'Client', 'Produit', 'Quantité', 'Prix Unitaire (FCFA)', 'Total (FCFA)', 'Date']);
                $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
                $endDate = $request->input('end_date', now()->toDateString());
                
                $items = OrderItem::with(['order', 'product'])
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    })->get();

                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->order_id,
                        $item->order->customer_name,
                        $item->product->name ?? 'Service',
                        $item->quantity,
                        $item->price,
                        $item->quantity * $item->price,
                        $item->created_at->toDateString()
                    ]);
                }
            } elseif ($type === 'stocks') {
                fputcsv($file, ['ID Produit', 'Nom', 'Stock Actuel', 'Prix Vente (FCFA)', 'Prix Achat (FCFA)', 'Valeur Stock Vente', 'Valeur Stock Achat']);
                $products = Product::all();
                foreach ($products as $p) {
                    fputcsv($file, [
                        $p->id,
                        $p->name,
                        $p->stock,
                        $p->price,
                        $p->purchase_price,
                        $p->stock * $p->price,
                        $p->stock * $p->purchase_price
                    ]);
                }
            } elseif ($type === 'profits') {
                fputcsv($file, ['Mois', 'Chiffre d\'Affaires (FCFA)', 'Coût d\'Achat (FCFA)', 'Bénéfice Brut (FCFA)', 'Marge (%)']);
                $startDate = $request->input('start_date', now()->subYear()->toDateString());
                $endDate = $request->input('end_date', now()->toDateString());

                $items = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                })->get()->groupBy(fn($i) => $i->created_at->format('Y-m'));

                foreach ($items as $month => $monthItems) {
                    $rev = $monthItems->sum(fn($i) => $i->quantity * $i->price);
                    $cogs = $monthItems->sum(fn($i) => $i->quantity * ($i->purchase_price ?: 0));
                    $profit = $rev - $cogs;
                    $margin = $rev > 0 ? ($profit / $rev) * 100 : 0;
                    fputcsv($file, [
                        $month,
                        $rev,
                        $cogs,
                        $profit,
                        number_format($margin, 2) . '%'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
