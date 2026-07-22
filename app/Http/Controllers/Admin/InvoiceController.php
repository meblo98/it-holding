<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->paginate(10);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $nextNumber = 'FAC-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $products = Product::where('active', true)->get(['id', 'name', 'price', 'purchase_price']);
        $services = Service::where('active', true)->get(['id', 'title', 'price']);
        
        $catalog = $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'purchase_price' => $p->purchase_price,
            'type' => 'product'
        ])->concat($services->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->title,
            'price' => $s->price,
            'purchase_price' => 0.00,
            'type' => 'service'
        ]));

        $clients = \App\Models\Client::orderBy('company_name')->orderBy('last_name')->get();

        return view('admin.invoices.create', compact('nextNumber', 'catalog', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|unique:invoices',
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable',
            'client_address' => 'nullable',
            'due_date' => 'nullable|date',
            'notes' => 'nullable',
            'payment_method' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $taxAmount = floatval($request->input('tax_amount', 0));

        $invoice = Invoice::create([
            'number'         => $validated['number'],
            'client_id'      => $validated['client_id'] ?? null,
            'client_name'    => $validated['client_name'],
            'client_email'   => $validated['client_email'] ?? null,
            'client_phone'   => $validated['client_phone'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'due_date'       => $validated['due_date'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'subtotal'       => $subtotal,
            'tax_amount'     => $taxAmount,
            'total_amount'   => $subtotal + $taxAmount,
            'share_token'    => Str::random(32),
        ]);

        foreach ($validated['items'] as $item) {
            $purchasePrice = 0;
            $productId = $item['product_id'] ?? null;
            
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $purchasePrice = $product->purchase_price;
                }
            } elseif (!empty($item['purchase_price'])) {
                $purchasePrice = $item['purchase_price'];
            } else {
                $product = Product::where('name', $item['description'])->first();
                if ($product) {
                    $productId = $product->id;
                    $purchasePrice = $product->purchase_price;
                }
            }

            $invoice->items()->create([
                'product_id'     => $productId,
                'description'    => $item['description'],
                'quantity'       => $item['quantity'],
                'unit_price'     => $item['unit_price'],
                'purchase_price' => $purchasePrice,
                'total_price'    => $item['quantity'] * $item['unit_price'],
            ]);

            // Save to catalog if requested
            if (!empty($item['save_to_catalog'])) {
                $this->saveToCatalog($item);
            }
        }

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Facture créée avec succès.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $products = Product::where('active', true)->get(['id', 'name', 'price', 'purchase_price']);
        $services = Service::where('active', true)->get(['id', 'title', 'price']);
        
        $catalog = $products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'purchase_price' => $p->purchase_price,
            'type' => 'product'
        ])->concat($services->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->title,
            'price' => $s->price,
            'purchase_price' => 0.00,
            'type' => 'service'
        ]));

        $clients = \App\Models\Client::orderBy('company_name')->orderBy('last_name')->get();

        return view('admin.invoices.edit', compact('invoice', 'catalog', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'number' => 'required|unique:invoices,number,' . $invoice->id,
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable',
            'client_address' => 'nullable',
            'due_date' => 'nullable|date',
            'notes' => 'nullable',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'payment_method' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $taxAmount = floatval($request->input('tax_amount', 0));

        $invoice->update([
            'number'         => $validated['number'],
            'client_id'      => $validated['client_id'] ?? null,
            'client_name'    => $validated['client_name'],
            'client_email'   => $validated['client_email'] ?? null,
            'client_phone'   => $validated['client_phone'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'due_date'       => $validated['due_date'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'status'         => $validated['status'],
            'payment_method' => $validated['payment_method'] ?? null,
            'subtotal'       => $subtotal,
            'tax_amount'     => $taxAmount,
            'total_amount'   => $subtotal + $taxAmount,
        ]);

        $invoice->items()->delete();
        foreach ($validated['items'] as $item) {
            $purchasePrice = 0;
            $productId = $item['product_id'] ?? null;
            
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $purchasePrice = $product->purchase_price;
                }
            } elseif (!empty($item['purchase_price'])) {
                $purchasePrice = $item['purchase_price'];
            } else {
                $product = Product::where('name', $item['description'])->first();
                if ($product) {
                    $productId = $product->id;
                    $purchasePrice = $product->purchase_price;
                }
            }

            $invoice->items()->create([
                'product_id'     => $productId,
                'description'    => $item['description'],
                'quantity'       => $item['quantity'],
                'unit_price'     => $item['unit_price'],
                'purchase_price' => $purchasePrice,
                'total_price'    => $item['quantity'] * $item['unit_price'],
            ]);

            // Save to catalog if requested
            if (!empty($item['save_to_catalog'])) {
                $this->saveToCatalog($item);
            }
        }

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Facture mise à jour avec succès.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success', 'Facture supprimée.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items');
        return view('admin.invoices.print', compact('invoice'));
    }

    public function share(Invoice $invoice)
    {
        if (!$invoice->share_token) {
            $invoice->update(['share_token' => Str::random(32)]);
        }
        $url = route('invoices.public_view', $invoice->share_token);
        return back()->with('share_url', $url);
    }

    public function publicView($token)
    {
        $invoice = Invoice::where('share_token', $token)->firstOrFail();
        $invoice->load('items');
        return view('admin.invoices.print', compact('invoice'));
    }

    public function printReceipt(Invoice $invoice)
    {
        $invoice->load('items');
        return view('admin.invoices.receipt', compact('invoice'));
    }

    public function createCreditNote(Invoice $invoice)
    {
        if ($invoice->type === 'credit_note') {
            return back()->with('error', 'Impossible de créer un avoir pour un avoir.');
        }

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Cette facture est déjà annulée.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($invoice) {
            // Create Credit Note Invoice
            $creditNoteNumber = 'AVO-' . date('Y') . '-' . str_pad(Invoice::where('type', 'credit_note')->count() + 1, 4, '0', STR_PAD_LEFT);
            
            $creditNote = Invoice::create([
                'number' => $creditNoteNumber,
                'type' => 'credit_note',
                'parent_invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'user_id' => auth()->id(),
                'client_name' => $invoice->client_name,
                'client_email' => $invoice->client_email,
                'client_phone' => $invoice->client_phone,
                'client_address' => $invoice->client_address,
                'subtotal' => $invoice->subtotal,
                'tax_amount' => $invoice->tax_amount,
                'total_amount' => $invoice->total_amount,
                'status' => 'paid',
                'due_date' => now(),
                'notes' => "Avoir généré pour la facture #" . $invoice->number,
                'share_token' => Str::random(32),
            ]);

            // Copy items
            foreach ($invoice->items as $item) {
                $creditNote->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'purchase_price' => $item->purchase_price ?? 0,
                ]);
            }

            // Update original invoice status to cancelled
            $invoice->update(['status' => 'cancelled']);

            // Deduct from client's current balance if they have a balance
            if ($invoice->client) {
                $invoice->client->decrement('current_balance', $invoice->total_amount);
            }
        });

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Avoir créé avec succès. La facture originale a été annulée et le solde du client a été ajusté.');
    }

    /**
     * Save an item description to the product or service catalog.
     */
    private function saveToCatalog(array $item): void
    {
        $type = $item['catalog_type'] ?? 'product';
        if ($type === 'service') {
            Service::firstOrCreate(
                ['title' => $item['description']],
                ['slug' => Str::slug($item['description']), 'price' => $item['unit_price'], 'active' => true]
            );
        } else {
            Product::firstOrCreate(
                ['name' => $item['description']],
                ['slug' => Str::slug($item['description']), 'price' => $item['unit_price'], 'active' => true]
            );
        }
    }
}
