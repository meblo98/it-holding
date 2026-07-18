<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Client;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticket::with(['client', 'technician', 'attachments']);

        // Scope queries based on user role
        if ($user->role === 'client') {
            $clientId = $user->client ? $user->client->id : null;
            if ($clientId) {
                $query->where('client_id', $clientId);
            } else {
                $query->where('client_email', $user->email);
            }
        } elseif ($user->role === 'technicien') {
            // Technicians see tickets assigned to them or unassigned
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(15);

        $tickets->getCollection()->transform(function ($ticket) {
            return $this->formatTicket($ticket);
        });

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'type' => 'required|in:repair,installation,maintenance,advice,warranty_claim',
            'product_name' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];

        // If staff, they can specify client and assignment
        if ($user->isStaff()) {
            $rules['client_id'] = 'nullable|exists:clients,id';
            $rules['assigned_to'] = 'nullable|exists:users,id';
            $rules['client_name'] = 'required|string|max:255';
            $rules['client_phone'] = 'nullable|string';
            $rules['client_email'] = 'nullable|email';
        }

        $validated = $request->validate($rules);

        $validated['number'] = Ticket::generateNumber();
        $validated['opened_at'] = now();
        
        if ($user->role === 'client') {
            $client = $user->client;
            $validated['client_id'] = $client ? $client->id : null;
            $validated['client_name'] = $user->name;
            $validated['client_phone'] = $user->phone;
            $validated['client_email'] = $user->email;
            $validated['status'] = 'open';
        } else {
            $validated['status'] = $request->input('status', 'open');
        }

        $ticket = Ticket::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'type' => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'document',
                ]);
            }
        }

        return response()->json([
            'message' => 'Ticket créé avec succès.',
            'ticket' => $this->formatTicket($ticket->load(['client', 'technician', 'attachments']))
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $ticket = Ticket::with(['client', 'technician', 'attachments'])->findOrFail($id);

        // Security check for clients
        if ($user->role === 'client') {
            $clientId = $user->client ? $user->client->id : null;
            if ($clientId && $ticket->client_id !== $clientId) {
                return response()->json(['message' => 'Non autorisé.'], 403);
            }
            if (!$clientId && $ticket->client_email !== $user->email) {
                return response()->json(['message' => 'Non autorisé.'], 403);
            }
        }

        return response()->json([
            'ticket' => $this->formatTicket($ticket)
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $ticket = Ticket::findOrFail($id);

        // Security check: Only staff (technicians, admin, commercial, etc.) can update ticket properties
        if (!$user->isStaff()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $rules = [
            'status' => 'required|in:open,diagnosed,in_progress,waiting_parts,resolved,closed,cancelled',
            'diagnosis' => 'nullable|string',
            'intervention_notes' => 'nullable|string',
            'parts_used' => 'nullable|string',
            'repair_cost' => 'nullable|numeric|min:0',
            'covered_by_warranty' => 'boolean',
            'notes' => 'nullable|string',
        ];

        // Admins/commercials can assign tickets or reschedule
        if ($user->isAdmin() || $user->isDG() || $user->isCommercial()) {
            $rules['assigned_to'] = 'nullable|exists:users,id';
            $rules['priority'] = 'nullable|in:low,normal,high,urgent';
            $rules['scheduled_date'] = 'nullable|date';
        }

        $validated = $request->validate($rules);

        if (isset($validated['status'])) {
            if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
                $validated['resolved_at'] = now();
            }
            if ($validated['status'] === 'closed' && !$ticket->closed_at) {
                $validated['closed_at'] = now();
            }
        }

        $ticket->update($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'type' => str_starts_with($file->getMimeType(), 'image') ? 'image' : 'document',
                ]);
            }
        }

        return response()->json([
            'message' => 'Ticket mis à jour avec succès.',
            'ticket' => $this->formatTicket($ticket->load(['client', 'technician', 'attachments']))
        ]);
    }

    private function formatTicket($ticket)
    {
        return [
            'id' => $ticket->id,
            'number' => $ticket->number,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'status_label' => Ticket::statusConfig($ticket->status)['label'] ?? $ticket->status,
            'priority' => $ticket->priority,
            'priority_label' => Ticket::priorityConfig($ticket->priority)['label'] ?? $ticket->priority,
            'type' => $ticket->type,
            'product_name' => $ticket->product_name,
            'serial_number' => $ticket->serial_number,
            'client_name' => $ticket->client_name,
            'client_phone' => $ticket->client_phone,
            'client_email' => $ticket->client_email,
            'diagnosis' => $ticket->diagnosis,
            'intervention_notes' => $ticket->intervention_notes,
            'parts_used' => $ticket->parts_used,
            'repair_cost' => $ticket->repair_cost,
            'covered_by_warranty' => (bool)$ticket->covered_by_warranty,
            'scheduled_date' => $ticket->scheduled_date ? $ticket->scheduled_date->format('Y-m-d') : null,
            'opened_at' => $ticket->opened_at ? $ticket->opened_at->toIso8601String() : null,
            'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->toIso8601String() : null,
            'closed_at' => $ticket->closed_at ? $ticket->closed_at->toIso8601String() : null,
            'technician' => $ticket->technician ? [
                'id' => $ticket->technician->id,
                'name' => $ticket->technician->name,
                'email' => $ticket->technician->email,
            ] : null,
            'attachments' => $ticket->attachments->map(function ($att) {
                return [
                    'id' => $att->id,
                    'file_name' => $att->file_name,
                    'file_url' => asset('storage/' . $att->file_path),
                    'type' => $att->type,
                ];
            })
        ];
    }
}
