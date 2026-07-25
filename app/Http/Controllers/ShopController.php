<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductOption;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id'    => 'nullable|integer|exists:brands,id',
            'condition'   => 'nullable|string|max:50',
            'blackfriday' => 'nullable|boolean',
        ]);

        $query = Product::where('active', true)->where(function ($q) {
            $q->where('stock', '>', 0)->orWhereNotNull('available_at');
        });

        if ($request->filled('category_id')) {
            $category = Category::with('children')->find((int) $request->category_id);
            if ($category) {
                $ids = $category->children->pluck('id')->prepend($category->id);
                $query->whereIn('category_id', $ids);
            }
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', (int) $request->brand_id);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('blackfriday')) {
            $query->where('blackfriday', true);
        }

        $products = $query->with('images', 'category', 'brand')->paginate(12)->withQueryString();

        // filters list — only root categories with their children
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->whereHas('products', function ($q) {
                $q->where('active', true)->where(function ($sub) {
                    $sub->where('stock', '>', 0)->orWhereNotNull('available_at');
                });
            })
            ->orWhereHas('children.products', function ($q) {
                $q->where('active', true)->where(function ($sub) {
                    $sub->where('stock', '>', 0)->orWhereNotNull('available_at');
                });
            })
            ->orderBy('name')
            ->get();
        $brands = Brand::whereHas('products', function ($q) {
            $q->where('active', true)->where(function ($sub) {
                $sub->where('stock', '>', 0)->orWhereNotNull('available_at');
            });
        })->get();
        $conditions = Product::where('active', true)
            ->where(function ($q) {
                $q->where('stock', '>', 0)->orWhereNotNull('available_at');
            })
            ->whereNotNull('condition')
            ->distinct()
            ->pluck('condition');

        return view('pages.shop.index', compact('products', 'categories', 'brands', 'conditions'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('active', true)->with('images', 'category', 'brand')->firstOrFail();
        return view('pages.shop.show', compact('product'));
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $id => $details) {
            $productId = $details['product_id'] ?? $id;
            $product = Product::find($productId);
            if ($product) {
                $optionSum = 0;
                if (!empty($details['options'])) {
                    foreach ($details['options'] as $opt) {
                        $optionSum += $opt['price'];
                    }
                }
                $cart[$id]['price'] = Product::calculateWholesalePrice($product, $details['quantity']) + $optionSum;
            }
            $total += $cart[$id]['price'] * $cart[$id]['quantity'];
        }
        Session::put('cart', $cart);

        // Apply promo code if set
        $discount = 0;
        $promoCode = null;
        if (Session::has('promo_code')) {
            $promoCode = \App\Models\PartnerPromoCode::where('code', Session::get('promo_code'))->where('is_active', true)->first();
            if ($promoCode) {
                $discount = ($total * $promoCode->discount_percent) / 100;
            } else {
                Session::forget('promo_code');
            }
        }
        $discountedTotal = $total - $discount;

        return view('pages.shop.cart', compact('cart', 'total', 'discount', 'discountedTotal', 'promoCode'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate quantity
        $quantity = $request->input('quantity', 1);
        if (!$product->isPreorderable() && $quantity > $product->stock) {
            return redirect()->back()->with('error', 'Stock insuffisant. Seulement ' . $product->stock . ' disponible(s).');
        }

        $cart = Session::get('cart', []);

        $selectedOptions = $request->input('options', []);
        $selectedOptions = array_filter($selectedOptions);

        $optionsDetails = [];
        $optionSum = 0;
        $optionIds = [];
        if (!empty($selectedOptions)) {
            foreach ($selectedOptions as $optId) {
                $opt = ProductOption::find($optId);
                if ($opt && $opt->product_id == $product->id) {
                    $optionsDetails[] = [
                        'id' => $opt->id,
                        'name' => $opt->name,
                        'value' => $opt->value,
                        'price' => (float)$opt->price
                    ];
                    $optionSum += (float)$opt->price;
                    $optionIds[] = $opt->id;
                }
            }
        }
        sort($optionIds);
        $cartKey = empty($optionIds) ? (string)$id : $id . '-' . implode('-', $optionIds);

        if (isset($cart[$cartKey])) {
            $newQuantity = $cart[$cartKey]['quantity'] + $quantity;
            if (!$product->isPreorderable() && $newQuantity > $product->stock) {
                return redirect()->back()->with('error', 'Stock insuffisant. Seulement ' . $product->stock . ' disponible(s).');
            }
            $cart[$cartKey]['quantity'] = $newQuantity;
            
            // Recalculate price dynamically based on new quantity
            $cart[$cartKey]['price'] = Product::calculateWholesalePrice($product, $newQuantity) + $optionSum;
        } else {
            // determine effective price dynamically based on initial quantity
            $effectivePrice = Product::calculateWholesalePrice($product, $quantity) + $optionSum;

            $cart[$cartKey] = [
                "product_id" => $product->id,
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $effectivePrice,
                "image" => $product->image ?: ($product->images->first()?->path ?? null),
                "slug" => $product->slug,
                "options" => $optionsDetails
            ];
        }

        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function updateCart(Request $request)
    {
        $cart = Session::get('cart', []);
        $id = $request->input('id');
        $quantity = $request->input('quantity', 1);

        if (isset($cart[$id])) {
            $productId = $cart[$id]['product_id'] ?? $id;
            $product = Product::findOrFail($productId);

            if (!$product->isPreorderable() && $quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant'
                ], 400);
            }

            if ($quantity <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $quantity;
                
                $optionSum = 0;
                if (!empty($cart[$id]['options'])) {
                    foreach ($cart[$id]['options'] as $opt) {
                        $optionSum += (float)$opt['price'];
                    }
                }
                $cart[$id]['price'] = Product::calculateWholesalePrice($product, $quantity) + $optionSum;
            }

            Session::put('cart', $cart);

            // Calculate new total
            $total = 0;
            foreach ($cart as $details) {
                $total += $details['price'] * $details['quantity'];
            }

            return response()->json([
                'success' => true,
                'total' => number_format($total, 0, ',', ' ') . ' FCFA',
                'cartCount' => array_sum(array_column($cart, 'quantity'))
            ]);
        }

        return response()->json(['success' => false], 404);
    }

    public function removeFromCart($id)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Produit retiré du panier.');
    }

    public function applyPromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $promoCode = \App\Models\PartnerPromoCode::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$promoCode) {
            return redirect()->back()->with('error', 'Code promo invalide ou expiré.');
        }

        Session::put('promo_code', $promoCode->code);

        return redirect()->back()->with('success', 'Code promo appliqué avec succès !');
    }

    public function removePromoCode()
    {
        Session::forget('promo_code');
        return redirect()->back()->with('success', 'Code promo retiré.');
    }

    public function toggleTva(Request $request)
    {
        $apply = (bool) $request->input('apply_tva');
        Session::put('apply_tva', $apply);
        return response()->json(['success' => true]);
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide.');
        }
        $total = 0;
        foreach ($cart as $id => $details) {
            $productId = $details['product_id'] ?? $id;
            $product = Product::find($productId);
            if ($product) {
                $optionSum = 0;
                if (!empty($details['options'])) {
                    foreach ($details['options'] as $opt) {
                        $optionSum += $opt['price'];
                    }
                }
                $cart[$id]['price'] = Product::calculateWholesalePrice($product, $details['quantity']) + $optionSum;
            }
            $total += $cart[$id]['price'] * $cart[$id]['quantity'];
        }

        // Apply promo code if set
        $discount = 0;
        $promoCode = null;
        if (Session::has('promo_code')) {
            $promoCode = \App\Models\PartnerPromoCode::where('code', Session::get('promo_code'))->where('is_active', true)->first();
            if ($promoCode) {
                $discount = ($total * $promoCode->discount_percent) / 100;
            } else {
                Session::forget('promo_code');
            }
        }
        $discountedTotal = $total - $discount;

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
        Session::put('cart', $cart);
        return view('pages.shop.checkout', compact('cart', 'total', 'discount', 'discountedTotal', 'promoCode', 'client'));
    }

    /**
     * Place order (Cash on Delivery)
     */
    public function placeOrder(Request $request)
    {
        $cart = Session::get('cart', []);
        if (count($cart) === 0) {
            return redirect()->route('shop.index')->with('error', 'Votre panier est vide.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'payment_method' => 'required|string',
        ]);

        // calculate total with dynamic wholesale pricing recalculation
        $total = 0;
        foreach ($cart as $id => $details) {
            $productId = $details['product_id'] ?? $id;
            $product = Product::find($productId);
            if ($product) {
                $optionSum = 0;
                if (!empty($details['options'])) {
                    foreach ($details['options'] as $opt) {
                        $optionSum += $opt['price'];
                    }
                }
                $cart[$id]['price'] = Product::calculateWholesalePrice($product, $details['quantity']) + $optionSum;
            }
            $total += $cart[$id]['price'] * $cart[$id]['quantity'];
        }

        // Apply promo code if set
        $discount = 0;
        $promoCode = null;
        if (Session::has('promo_code')) {
            $promoCode = \App\Models\PartnerPromoCode::where('code', Session::get('promo_code'))->where('is_active', true)->first();
            if ($promoCode) {
                $discount = ($total * $promoCode->discount_percent) / 100;
            }
        }
        $subtotalAfterDiscount = $total - $discount;

        // Calculate tax/TVA if requested
        $taxAmount = 0;
        if (Session::get('apply_tva', false)) {
            $taxAmount = $subtotalAfterDiscount * 0.18;
        }
        $grandTotal = $subtotalAfterDiscount + $taxAmount;

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

        // Check wallet balance if wallet payment chosen
        if ($validated['payment_method'] === 'wallet') {
            if (!$client) {
                return redirect()->back()->with('error', 'Vous devez être enregistré comme client pour payer avec le portefeuille.');
            }
            if ($client->wallet_balance < $grandTotal) {
                return redirect()->back()->with('error', 'Solde du portefeuille insuffisant. Solde : ' . number_format($client->wallet_balance, 0) . ' FCFA.');
            }
        }

        // Check credit limit if credit payment chosen
        if ($validated['payment_method'] === 'credit') {
            if (!$client || !$client->is_professional) {
                return redirect()->back()->with('error', 'Seuls les clients professionnels enregistrés peuvent commander à crédit.');
            }
            if (($client->current_balance + $grandTotal) > $client->credit_limit) {
                return redirect()->back()->with('error', 'Plafond de crédit dépassé. Solde dû + Commande : ' . number_format($client->current_balance + $grandTotal, 0) . ' / ' . number_format($client->credit_limit, 0) . ' FCFA.');
            }
        }

        // create order within transaction
        DB::beginTransaction();
        try {
            $fullAddress = $validated['address'] . ', ' . $validated['city'] . ' ' . $validated['zip'] . ', ' . $validated['country'];
            
            $paymentStatus = 'unpaid';
            if ($validated['payment_method'] === 'wallet') {
                $paymentStatus = 'paid';
                $client->decrement('wallet_balance', $grandTotal);
            } elseif ($validated['payment_method'] === 'credit') {
                $client->increment('current_balance', $grandTotal);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'client_id' => $client ? $client->id : null,
                'customer_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
                'customer_address' => $fullAddress,
                'total_amount' => $grandTotal,
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'promo_code_id' => $promoCode ? $promoCode->id : null,
                'discount_amount' => $discount,
                'tax_amount' => $taxAmount,
            ]);

            if ($validated['payment_method'] === 'wallet') {
                \App\Models\WalletTransaction::create([
                    'client_id' => $client->id,
                    'type' => 'payment',
                    'amount' => $grandTotal,
                    'description' => "Paiement de la commande #" . $order->id,
                    'transaction_date' => now(),
                    'order_id' => $order->id,
                ]);
            }

            // Create the Partner Commission record if a promo code was used
            if ($promoCode) {
                $commissionAmount = ($total * $promoCode->commission_percent) / 100;
                \App\Models\PartnerCommission::create([
                    'partner_id' => $promoCode->partner_id,
                    'order_id' => $order->id,
                    'promo_code_id' => $promoCode->id,
                    'order_amount' => $total,
                    'commission_amount' => $commissionAmount,
                    'status' => 'pending',
                ]);
            }

            foreach ($cart as $id => $details) {
                $productId = $details['product_id'] ?? $id;
                $product = Product::findOrFail($productId);

                // prevent ordering more than stock if not preorderable
                $quantity = $product->isPreorderable() ? $details['quantity'] : min($details['quantity'], $product->stock);

                // Calculate final dynamic unit price to save
                $optionSum = 0;
                if (!empty($details['options'])) {
                    foreach ($details['options'] as $opt) {
                        $optionSum += $opt['price'];
                    }
                }
                $finalItemPrice = Product::calculateWholesalePrice($product, $quantity) + $optionSum;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $finalItemPrice,
                    'purchase_price' => $product->purchase_price ?? 0.00,
                    'options' => !empty($details['options']) ? $details['options'] : null,
                    'is_preorder' => $product->isPreorderable(),
                ]);

                // reduce stock
                $product->decrement('stock', $quantity);

                // Log stock movement
                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'quantity'   => -$quantity,
                    'type'       => 'out',
                    'source'     => "Commande #" . $order->id . " (Client: {$order->customer_name})",
                    'notes'      => $product->isPreorderable() ? "Précommande en ligne" : "Achat en ligne",
                ]);
            }

            DB::commit();

            try {
                \App\Services\WhatsAppService::notifyAdminForOrder($order);
            } catch (\Exception $e) {
                Log::error("Error sending WhatsApp notification: " . $e->getMessage());
            }

            // clear cart and promo code
            Session::forget('cart');
            Session::forget('promo_code');
            Session::forget('apply_tva');

            $successMsg = 'Commande passée.';
            if ($validated['payment_method'] === 'wallet') {
                $successMsg .= ' Payée via votre portefeuille.';
            } elseif ($validated['payment_method'] === 'credit') {
                $successMsg .= ' Facturée à terme sur votre crédit professionnel.';
            } else {
                $successMsg .= ' Paiement à la livraison.';
            }

            return redirect()->route('shop.thanks', $order->id)->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création commande', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'cart'    => $cart,
            ]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la commande.');
        }
    }

    /**
     * Thank you / order confirmation
     */
    public function thanks(Order $order)
    {
        $order->load('items.product');
        return view('pages.shop.thanks', compact('order'));
    }

    public function requestQuote(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'nullable|array',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $quantity = $validated['quantity'];

        $selectedOptions = $request->input('options', []);
        $selectedOptions = array_filter($selectedOptions);

        $optionSum = 0;
        $optStrings = [];
        $optionsDetails = [];
        if (!empty($selectedOptions)) {
            foreach ($selectedOptions as $optId) {
                $opt = ProductOption::find($optId);
                if ($opt && $opt->product_id == $product->id) {
                    $optionSum += (float)$opt->price;
                    $optStrings[] = $opt->name . ': ' . $opt->value;
                    $optionsDetails[] = [
                        'id' => $opt->id,
                        'name' => $opt->name,
                        'value' => $opt->value,
                        'price' => (float)$opt->price
                    ];
                }
            }
        }

        $unitPrice = Product::calculateWholesalePrice($product, $quantity) + $optionSum;
        $totalPrice = $unitPrice * $quantity;

        // Get or create client profile
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour demander un devis.');
        }

        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client) {
            $names = explode(' ', $user->name, 2);
            $client = \App\Models\Client::create([
                'user_id' => $user->id,
                'first_name' => $names[0] ?? 'Client',
                'last_name' => $names[1] ?? 'Client',
                'email' => $user->email,
                'phone' => $user->phone ?? '770000000',
                'wallet_balance' => 0,
                'current_balance' => 0,
            ]);
        }

        // Create Quote
        $nextNumber = 'DEV-' . date('Y') . '-' . str_pad(\App\Models\Quote::count() + 1, 4, '0', STR_PAD_LEFT);
        
        $description = $product->name;
        if (!empty($optStrings)) {
            $description .= ' (' . implode(', ', $optStrings) . ')';
        }

        DB::beginTransaction();
        try {
            $quote = \App\Models\Quote::create([
                'number' => $nextNumber,
                'client_id' => $client->id,
                'client_name' => $client->first_name . ' ' . $client->last_name,
                'client_email' => $client->email,
                'client_phone' => $client->phone,
                'client_address' => $user->address ?? 'Dakar, Sénégal',
                'valid_until' => now()->addDays(30),
                'notes' => "Demande de devis en ligne pour produit sur mesure.",
                'subtotal' => $totalPrice,
                'tax_amount' => 0,
                'total_amount' => $totalPrice,
                'status' => 'draft',
                'share_token' => Str::random(32),
            ]);

            $quote->items()->create([
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            DB::commit();

            return redirect()->route('dashboard.orders')->with('success', 'Votre demande de devis personnalisé (' . $quote->number . ') a été soumise avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur demande devis personnalisé', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la création de la demande de devis.');
        }
    }
}
