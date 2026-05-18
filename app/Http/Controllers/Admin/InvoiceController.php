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
        $products = Product::where('active', true)->get(['id', 'name', 'price']);
        $services = Service::where('active', true)->get(['id', 'title', 'price']);
        
        $catalog = $products->map(fn($p) => ['name' => $p->name, 'price' => $p->price, 'type' => 'product'])
            ->concat($services->map(fn($s) => ['name' => $s->title, 'price' => $s->price, 'type' => 'service']));

        return view('admin.invoices.create', compact('nextNumber', 'catalog'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|unique:invoices',
            'client_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable',
            'client_address' => 'nullable',
            'due_date' => 'nullable|date',
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

        $invoice = Invoice::create([
            'number'         => $validated['number'],
            'client_name'    => $validated['client_name'],
            'client_email'   => $validated['client_email'] ?? null,
            'client_phone'   => $validated['client_phone'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'due_date'       => $validated['due_date'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'subtotal'       => $subtotal,
            'tax_amount'     => 0,
            'total_amount'   => $subtotal,
            'share_token'    => Str::random(32),
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
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
        $products = Product::where('active', true)->get(['id', 'name', 'price']);
        $services = Service::where('active', true)->get(['id', 'title', 'price']);
        
        $catalog = $products->map(fn($p) => ['name' => $p->name, 'price' => $p->price, 'type' => 'product'])
            ->concat($services->map(fn($s) => ['name' => $s->title, 'price' => $s->price, 'type' => 'service']));

        return view('admin.invoices.edit', compact('invoice', 'catalog'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'number' => 'required|unique:invoices,number,' . $invoice->id,
            'client_name' => 'required',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable',
            'client_address' => 'nullable',
            'due_date' => 'nullable|date',
            'notes' => 'nullable',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $invoice->update([
            'number'         => $validated['number'],
            'client_name'    => $validated['client_name'],
            'client_email'   => $validated['client_email'] ?? null,
            'client_phone'   => $validated['client_phone'] ?? null,
            'client_address' => $validated['client_address'] ?? null,
            'due_date'       => $validated['due_date'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'status'         => $validated['status'],
            'subtotal'       => $subtotal,
            'total_amount'   => $subtotal,
        ]);

        $invoice->items()->delete();
        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
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
