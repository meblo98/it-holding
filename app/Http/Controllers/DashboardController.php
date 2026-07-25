<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user && ($user->isAdmin() || $user->isStaff())) {
                return redirect()->route('admin.dashboard');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        
        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->whereIn('status', ['pending', 'processing', 'in_progress'])->count();
        $completedOrders = $user->orders()->whereIn('status', ['completed', 'delivered'])->count();
        
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        $activeWarrantiesCount = 0;
        if ($client) {
            $activeWarrantiesCount = \App\Models\Warranty::where('client_id', $client->id)
                ->where('status', 'active')
                ->count();
        }

        $recentOrders = $user->orders()->latest()->take(7)->get();
        
        // Mock browsing history with latest products
        $browsingHistory = Product::where('active', true)->latest()->take(4)->get();
        
        return view('dashboard', compact(
            'user', 
            'totalOrders', 
            'pendingOrders', 
            'completedOrders', 
            'activeWarrantiesCount',
            'recentOrders',
            'browsingHistory'
        ));
    }

    public function warranties()
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        
        if ($client) {
            $warranties = \App\Models\Warranty::where('client_id', $client->id)
                ->with(['product', 'order'])
                ->orderByDesc('purchase_date')
                ->paginate(10);
        } else {
            $warranties = \App\Models\Warranty::whereRaw('1=0')->paginate(10);
        }
        
        return view('pages.shop.warranties', compact('user', 'warranties'));
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()->latest()->paginate(10);
        
        return view('pages.shop.orders', compact('user', 'orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product']);
        
        return view('pages.shop.order-details', compact('order'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('pages.shop.settings', compact('user'));
    }

    public function trackOrder()
    {
        $user = Auth::user();
        return view('pages.shop.track-order', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'display_name' => 'required|string|max:255',
            'username'     => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email'        => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'country'      => 'nullable|string|max:2',
            'photo'        => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->display_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
        ];

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updateAddress(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'billing_first_name' => 'nullable|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_zip' => 'nullable|string|max:255',
            'shipping_first_name' => 'nullable|string|max:255',
            'shipping_last_name' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:255',
        ]);

        $user->update($data);

        return back()->with('success', 'Adresses mises à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function partner()
    {
        $user = Auth::user();
        $promoCodes = $user->promoCodes;
        $commissions = $user->commissions()->with(['order', 'promoCode'])->latest()->paginate(10);

        $totalEarned = $user->totalCommissionsEarned();
        $totalPending = $user->totalCommissionsPending();

        $client = \App\Models\Client::where('user_id', $user->id)->first();

        return view('pages.shop.partner', compact('user', 'promoCodes', 'commissions', 'totalEarned', 'totalPending', 'client'));
    }

    public function generatePromoCode(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'code' => 'required|string|alpha_num|min:3|max:20|unique:partner_promo_codes,code',
        ]);

        $code = strtoupper($request->code);

        \App\Models\PartnerPromoCode::create([
            'partner_id' => $user->id,
            'code' => $code,
            'discount_percent' => 5.00, // 5% discount for buyers
            'commission_percent' => 10.00, // 10% commission for partners
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Votre code promo partenaire "' . $code . '" a été créé avec succès !');
    }

    public function savings()
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        
        $savingPlans = collect();
        if ($client) {
            $savingPlans = \App\Models\SavingPlan::where('client_id', $client->id)
                ->with(['product', 'service'])
                ->orderByDesc('created_at')
                ->paginate(10);
        } else {
            $savingPlans = \App\Models\SavingPlan::whereRaw('1=0')->paginate(10);
        }
        
        return view('pages.shop.savings.index', compact('user', 'savingPlans', 'client'));
    }

    public function createSavingPlan(Request $request)
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client) {
            $names = explode(' ', $user->name, 2);
            $client = \App\Models\Client::create([
                'user_id'         => $user->id,
                'first_name'      => $names[0] ?? 'Client',
                'last_name'       => $names[1] ?? 'Client',
                'email'           => $user->email,
                'phone'           => $user->phone ?? '770000000',
                'wallet_balance'  => 0,
                'current_balance' => 0,
            ]);
        }

        $product = null;
        $service = null;
        $targetAmount = 0;

        if ($request->has('product_id')) {
            $product = Product::findOrFail($request->product_id);
            $targetAmount = $product->promo_price ?: $product->price;
        } elseif ($request->has('service_id')) {
            $service = \App\Models\Service::findOrFail($request->service_id);
            $targetAmount = $service->price;
        }

        return view('pages.shop.savings.create', compact('user', 'client', 'product', 'service', 'targetAmount'));
    }

    public function storeSavingPlan(Request $request)
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client) {
            return back()->with('error', 'Profil client requis.');
        }

        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'service_id' => 'nullable|exists:services,id',
            'initial_deposit' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $product = null;
        $service = null;
        $targetAmount = 0;

        if ($request->filled('product_id')) {
            $product = Product::findOrFail($request->product_id);
            $targetAmount = $product->promo_price ?: $product->price;
        } elseif ($request->filled('service_id')) {
            $service = \App\Models\Service::findOrFail($request->service_id);
            $targetAmount = $service->price;
        } else {
            return back()->with('error', 'Veuillez sélectionner un produit ou un service.');
        }

        $initialDeposit = (float)$request->initial_deposit;

        if ($initialDeposit > $targetAmount) {
            return back()->withErrors(['initial_deposit' => 'Le dépôt initial ne peut pas dépasser le montant cible.']);
        }

        if ($request->payment_method === 'wallet' && $client->wallet_balance < $initialDeposit) {
            return back()->with('error', 'Solde de portefeuille insuffisant.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $savingPlan = \App\Models\SavingPlan::create([
                'client_id' => $client->id,
                'product_id' => $product ? $product->id : null,
                'service_id' => $service ? $service->id : null,
                'target_amount' => $targetAmount,
                'current_amount' => 0, // will increment with deposit
                'status' => 'active',
            ]);

            if ($initialDeposit > 0) {
                // Deduct from wallet if wallet selected
                if ($request->payment_method === 'wallet') {
                    $client->decrement('wallet_balance', $initialDeposit);
                    \App\Models\WalletTransaction::create([
                        'client_id' => $client->id,
                        'type' => 'payment',
                        'amount' => $initialDeposit,
                        'description' => "Dépôt initial épargne plan #" . $savingPlan->id,
                        'transaction_date' => now(),
                    ]);
                }

                $savingPlan->increment('current_amount', $initialDeposit);
                \App\Models\SavingTransaction::create([
                    'saving_plan_id' => $savingPlan->id,
                    'amount' => $initialDeposit,
                    'type' => 'deposit',
                    'payment_method' => $request->payment_method,
                ]);

                // Check if target reached
                if ($savingPlan->current_amount >= $savingPlan->target_amount) {
                    $savingPlan->update(['status' => 'completed']);
                    $this->triggerGoalDelivery($savingPlan);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('dashboard.savings.show', $savingPlan->id)->with('success', 'Plan d\'épargne créé avec succès.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Saving plan store error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création du plan d\'épargne.');
        }
    }

    public function showSavingPlan(\App\Models\SavingPlan $savingPlan)
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client || $savingPlan->client_id !== $client->id) {
            abort(403);
        }

        $savingPlan->load(['product', 'service', 'transactions']);
        return view('pages.shop.savings.show', compact('user', 'client', 'savingPlan'));
    }

    public function depositSavingPlan(Request $request, \App\Models\SavingPlan $savingPlan)
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client || $savingPlan->client_id !== $client->id) {
            abort(403);
        }

        if ($savingPlan->status !== 'active') {
            return back()->with('error', 'Ce plan d\'épargne n\'est plus actif.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);

        $amount = (float)$request->amount;

        // Limit deposit to remaining target amount
        $remaining = $savingPlan->target_amount - $savingPlan->current_amount;
        if ($amount > $remaining) {
            $amount = $remaining;
        }

        if ($request->payment_method === 'wallet' && $client->wallet_balance < $amount) {
            return back()->with('error', 'Solde de portefeuille insuffisant.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            if ($request->payment_method === 'wallet') {
                $client->decrement('wallet_balance', $amount);
                \App\Models\WalletTransaction::create([
                    'client_id' => $client->id,
                    'type' => 'payment',
                    'amount' => $amount,
                    'description' => "Dépôt épargne plan #" . $savingPlan->id,
                    'transaction_date' => now(),
                ]);
            }

            $savingPlan->increment('current_amount', $amount);
            \App\Models\SavingTransaction::create([
                'saving_plan_id' => $savingPlan->id,
                'amount' => $amount,
                'type' => 'deposit',
                'payment_method' => $request->payment_method,
            ]);

            // Check if target reached
            if ($savingPlan->current_amount >= $savingPlan->target_amount) {
                $savingPlan->update(['status' => 'completed']);
                $this->triggerGoalDelivery($savingPlan);
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Dépôt effectué avec succès !');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Saving plan deposit error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du dépôt.');
        }
    }

    public function withdrawSavingPlan(Request $request, \App\Models\SavingPlan $savingPlan)
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client || $savingPlan->client_id !== $client->id) {
            abort(403);
        }

        if ($savingPlan->status !== 'active') {
            return back()->with('error', 'Seuls les plans actifs peuvent être retirés.');
        }

        $withdrawAmount = $savingPlan->current_amount;
        if ($withdrawAmount <= 0) {
            $savingPlan->update(['status' => 'withdrawn']);
            return back()->with('success', 'Plan d\'épargne clôturé (sans fonds).');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Refund to client wallet balance
            $client->increment('wallet_balance', $withdrawAmount);
            \App\Models\WalletTransaction::create([
                'client_id' => $client->id,
                'type' => 'refund',
                'amount' => $withdrawAmount,
                'description' => "Retrait épargne plan #" . $savingPlan->id,
                'transaction_date' => now(),
            ]);

            // Log withdrawal on saving plan
            \App\Models\SavingTransaction::create([
                'saving_plan_id' => $savingPlan->id,
                'amount' => -$withdrawAmount,
                'type' => 'withdrawal',
                'payment_method' => 'wallet',
            ]);

            $savingPlan->update([
                'current_amount' => 0,
                'status' => 'withdrawn'
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Épargne retirée avec succès ! Les fonds (' . number_format($withdrawAmount, 0, ',', ' ') . ' FCFA) ont été crédités sur votre portefeuille client.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Saving plan withdraw error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du retrait de l\'épargne.');
        }
    }

    private function triggerGoalDelivery(\App\Models\SavingPlan $savingPlan)
    {
        $client = $savingPlan->client;
        $user = $client->user;

        // Generate paid invoice
        $invoiceNumber = 'FAC-' . date('Y') . '-' . str_pad(\App\Models\Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $invoice = \App\Models\Invoice::create([
            'number' => $invoiceNumber,
            'client_id' => $client->id,
            'user_id' => $user->id,
            'client_name' => $client->full_name,
            'client_email' => $client->email,
            'client_phone' => $client->phone,
            'client_address' => $client->address ?: 'N/A',
            'subtotal' => $savingPlan->target_amount,
            'tax_amount' => 0,
            'total_amount' => $savingPlan->target_amount,
            'status' => 'paid',
            'due_date' => now(),
            'notes' => "Facture générée pour finalisation du Plan d'Épargne #" . $savingPlan->id,
            'payment_method' => 'epargne',
            'share_token' => \Illuminate\Support\Str::random(32),
        ]);

        if ($savingPlan->product_id) {
            $product = $savingPlan->product;
            // Create paid order
            $order = Order::create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'customer_name' => $client->full_name,
                'customer_email' => $client->email,
                'customer_phone' => $client->phone,
                'customer_address' => $client->address ?: 'N/A',
                'total_amount' => $savingPlan->target_amount,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'epargne',
            ]);

            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $savingPlan->target_amount,
                'purchase_price' => $product->purchase_price ?? 0,
            ]);

            $invoice->update(['quote_id' => null]);

            $invoice->items()->create([
                'product_id' => $product->id,
                'description' => $product->name,
                'quantity' => 1,
                'unit_price' => $savingPlan->target_amount,
                'purchase_price' => $product->purchase_price ?? 0,
                'total_price' => $savingPlan->target_amount,
            ]);

            // Reduce stock
            $product->decrement('stock', 1);

            // Log stock movement
            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'quantity' => -1,
                'type' => 'out',
                'source' => "Plan d'Épargne #" . $savingPlan->id,
                'notes' => "Livraison automatique après objectif d'épargne atteint",
            ]);

            try {
                \App\Services\WhatsAppService::notifyAdminForOrder($order);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error sending WhatsApp notification for savings plan order: " . $e->getMessage());
            }
        } elseif ($savingPlan->service_id) {
            $service = $savingPlan->service;

            // Create Maintenance Contract automatically
            $contractNumber = \App\Models\MaintenanceContract::generateNumber();
            \App\Models\MaintenanceContract::create([
                'number' => $contractNumber,
                'client_id' => $client->id,
                'client_name' => $client->full_name,
                'client_phone' => $client->phone,
                'client_address' => $client->address ?: 'N/A',
                'type' => 'standard',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'price' => $savingPlan->target_amount,
                'billing_period' => 'annuel',
                'interventions_included' => 12,
                'interventions_used' => 0,
                'response_time_hours' => 24,
                'scope' => "Contrat de service souscrit automatiquement via l'Épargne Service pour : " . $service->title,
                'status' => 'active',
                'payment_status' => 'paid',
                'amount_paid' => $savingPlan->target_amount,
                'notes' => "Généré via Plan d'Épargne #" . $savingPlan->id,
            ]);

            $invoice->items()->create([
                'product_id' => null,
                'description' => "Service: " . $service->title,
                'quantity' => 1,
                'unit_price' => $savingPlan->target_amount,
                'purchase_price' => 0,
                'total_price' => $savingPlan->target_amount,
            ]);
        }
    }
}
