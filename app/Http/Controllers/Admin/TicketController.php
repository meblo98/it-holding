<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\Product;
use App\Models\Warranty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status');
        $priority = $request->input('priority');

        $tickets = Ticket::with(['client', 'technician'])
            ->when($search, fn($q, $s) => $q->where(fn($q) => $q
                ->where('number', 'like', "%$s%")
                ->orWhere('title', 'like', "%$s%")
                ->orWhere('client_name', 'like', "%$s%")
            ))
            ->when($status,   fn($q) => $q->where('status',   $status))
            ->when($priority, fn($q) => $q->where('priority', $priority))
            ->latest()
            ->paginate(20);

        $stats = [
            'open'        => Ticket::open()->count(),
            'in_progress' => Ticket::inProgress()->count(),
            'resolved'    => Ticket::resolved()->count(),
            'total'       => Ticket::count(),
        ];

        $technicians = User::whereIn('role', ['technicien', 'admin'])->orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'search', 'status', 'priority', 'stats', 'technicians'));
    }

    public function create(Request $request)
    {
        $clients     = Client::orderBy('first_name')->get();
        $products    = Product::orderBy('name')->get(['id', 'name']);
        $warranties  = Warranty::active()->with('client')->latest()->get();
        $technicians = User::whereIn('role', ['technicien', 'admin'])->orderBy('name')->get();

        return view('admin.tickets.create', compact('clients', 'products', 'warranties', 'technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'         => 'nullable|exists:clients,id',
            'warranty_id'       => 'nullable|exists:warranties,id',
            'product_id'        => 'nullable|exists:products,id',
            'assigned_to'       => 'nullable|exists:users,id',
            'client_name'       => 'required|string|max:255',
            'client_phone'      => 'nullable|string|max:50',
            'client_email'      => 'nullable|email',
            'product_name'      => 'nullable|string|max:255',
            'serial_number'     => 'nullable|string|max:255',
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'status'            => 'required|in:open,diagnosed,in_progress,waiting_parts,resolved,closed,cancelled',
            'priority'          => 'required|in:low,normal,high,urgent',
            'type'              => 'required|in:repair,installation,maintenance,advice,warranty_claim',
            'scheduled_date'    => 'nullable|date',
            'covered_by_warranty' => 'boolean',
            'notes'             => 'nullable|string',
            'attachments.*'     => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $validated['number']     = Ticket::generateNumber();
        $validated['opened_at']  = now();
        $validated['covered_by_warranty'] = $request->boolean('covered_by_warranty');

        $ticket = Ticket::create($validated);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'type'      => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'document',
                ]);
            }
        }

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', "Ticket {$ticket->number} créé avec succès.");
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['client', 'warranty', 'product', 'technician', 'attachments']);
        $technicians = User::whereIn('role', ['technicien', 'admin'])->orderBy('name')->get();
        return view('admin.tickets.show', compact('ticket', 'technicians'));
    }

    public function edit(Ticket $ticket)
    {
        $clients     = Client::orderBy('first_name')->get();
        $products    = Product::orderBy('name')->get(['id', 'name']);
        $warranties  = Warranty::active()->latest()->get();
        $technicians = User::whereIn('role', ['technicien', 'admin'])->orderBy('name')->get();
        return view('admin.tickets.edit', compact('ticket', 'clients', 'products', 'warranties', 'technicians'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'client_id'            => 'nullable|exists:clients,id',
            'warranty_id'          => 'nullable|exists:warranties,id',
            'assigned_to'          => 'nullable|exists:users,id',
            'client_name'          => 'required|string|max:255',
            'client_phone'         => 'nullable|string|max:50',
            'product_name'         => 'nullable|string|max:255',
            'serial_number'        => 'nullable|string|max:255',
            'title'                => 'required|string|max:255',
            'description'          => 'required|string',
            'status'               => 'required|in:open,diagnosed,in_progress,waiting_parts,resolved,closed,cancelled',
            'priority'             => 'required|in:low,normal,high,urgent',
            'type'                 => 'required|in:repair,installation,maintenance,advice,warranty_claim',
            'diagnosis'            => 'nullable|string',
            'intervention_notes'   => 'nullable|string',
            'parts_used'           => 'nullable|string',
            'repair_cost'          => 'nullable|numeric|min:0',
            'covered_by_warranty'  => 'boolean',
            'scheduled_date'       => 'nullable|date',
            'notes'                => 'nullable|string',
        ]);

        $validated['covered_by_warranty'] = $request->boolean('covered_by_warranty');

        // Update resolved/closed timestamps
        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }
        if ($validated['status'] === 'closed' && !$ticket->closed_at) {
            $validated['closed_at'] = now();
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'type'      => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'document',
                ]);
            }
        }

        $ticket->update($validated);

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', 'Ticket mis à jour.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket supprimé.');
    }
}
