<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::latest()->paginate(10);
        return view('admin.quotes.index', compact('quotes'));
    }

    public function create()
    {
        $nextNumber = 'DEV-' . date('Y') . '-' . str_pad(Quote::count() + 1, 4, '0', STR_PAD_LEFT);
        $products = Product::where('active', true)->get(['id', 'name', 'price']);
        $services = Service::where('active', true)->get(['id', 'title', 'price']);
        
        // Combine into a single catalog for easier frontend handling
        $catalog = $products->map(fn($p) => ['name' => $p->name, 'price' => $p->price, 'type' => 'product'])
            ->concat($services->map(fn($s) => ['name' => $s->title, 'price' => $s->price, 'type' => 'service']));

        return view('admin.quotes.create', compact('nextNumber', 'catalog'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|unique:quotes',
            'client_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable',
            'client_address' => 'nullable',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $quote = Quote::create([
            'number'        => $validated['number'],
            'client_name'   => $validated['client_name'],
            'client_email'  => $validated['client_email'] ?? null,
            'client_phone'  => $validated['client_phone'] ?? null,
            'client_address'=> $validated['client_address'] ?? null,
            'valid_until'   => $validated['valid_until'] ?? null,
            'notes'         => $validated['notes'] ?? null,
            'subtotal'      => $subtotal,
            'tax_amount'    => 0,
            'total_amount'  => $subtotal,
            'share_token'   => Str::random(32),
        ]);

        foreach ($validated['items'] as $item) {
            $quote->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);

            // Save to catalog if requested
            if (!empty($item['save_to_catalog'])) {
                $this->saveToCatalog($item);
            }
        }

        return redirect()->route('admin.quotes.show', $quote->id)->with('success', 'Devis créé avec succès.');
    }

    public function show(Quote $quote)
    {
        $quote->load('items');
        return view('admin.quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $quote->load('items');
        $products = Product::where('active', true)->get(['id', 'name', 'price']);
        $services = Service::where('active', true)->get(['id', 'title', 'price']);
        
        $catalog = $products->map(fn($p) => ['name' => $p->name, 'price' => $p->price, 'type' => 'product'])
            ->concat($services->map(fn($s) => ['name' => $s->title, 'price' => $s->price, 'type' => 'service']));

        return view('admin.quotes.edit', compact('quote', 'catalog'));
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'number' => 'required|unique:quotes,number,' . $quote->id,
            'client_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable',
            'client_address' => 'nullable',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable',
            'status' => 'required|in:draft,sent,accepted,rejected,expired,converted',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $quote->update([
            'number'         => $validated['number'],
            'client_name'    => $validated['client_name'],
            'client_email'   => $validated['client_email'] ?? null,
            'client_phone'   => $validated['client_phone'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'valid_until'    => $validated['valid_until'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'status'         => $validated['status'],
            'subtotal'       => $subtotal,
            'total_amount'   => $subtotal,
        ]);

        $quote->items()->delete();
        foreach ($validated['items'] as $item) {
            $quote->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);

            // Save to catalog if requested
            if (!empty($item['save_to_catalog'])) {
                $this->saveToCatalog($item);
            }
        }

        return redirect()->route('admin.quotes.show', $quote->id)->with('success', 'Devis mis à jour avec succès.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('admin.quotes.index')->with('success', 'Devis supprimé.');
    }

    public function print(Quote $quote)
    {
        $quote->load('items');
        return view('admin.quotes.print', compact('quote'));
    }

    public function share(Quote $quote)
    {
        if (!$quote->share_token) {
            $quote->update(['share_token' => Str::random(32)]);
        }
        $url = route('quotes.public_view', $quote->share_token);
        return back()->with('share_url', $url);
    }

    public function convert(Quote $quote)
    {
        if ($quote->status === 'converted') {
            return back()->with('error', 'Ce devis a déjà été converti.');
        }

        $invoiceId = DB::transaction(function () use ($quote) {
            $nextNumber = 'FAC-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'number'         => $nextNumber,
                'quote_id'       => $quote->id,
                'client_name'    => $quote->client_name,
                'client_email'   => $quote->client_email,
                'client_phone'   => $quote->client_phone,
                'client_address' => $quote->client_address,
                'subtotal'       => $quote->subtotal,
                'tax_amount'     => $quote->tax_amount,
                'total_amount'   => $quote->total_amount,
                'status'         => 'draft',
                'due_date'       => now()->addDays(30),
                'share_token'    => Str::random(32),
            ]);

            foreach ($quote->items as $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            $quote->update(['status' => 'converted']);

            return $invoice->id;
        });

        return redirect()->route('admin.invoices.show', $invoiceId)
            ->with('success', 'Devis converti en facture avec succès.');
    }

    public function publicView($token)
    {
        $quote = Quote::where('share_token', $token)->firstOrFail();
        $quote->load('items');
        return view('admin.quotes.print', compact('quote')); // Reuse print view for public view
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
