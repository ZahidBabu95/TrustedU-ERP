<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * GET /api/tickets — List tickets
     */
    public function index(Request $request): JsonResponse
    {
        $query = SupportTicket::with(['client:id,name', 'assignee:id,name'])
            ->withCount('messages');

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('ticket_number', 'like', "%{$request->search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderByDesc('created_at')->paginate($request->per_page ?? 20),
        ]);
    }

    /**
     * POST /api/tickets — Create ticket
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'in:low,medium,high,urgent',
            'client_id'   => 'nullable|exists:clients,id',
            'email'       => 'nullable|email',
            'category'    => 'nullable|string',
        ]);

        $validated['ticket_number'] = 'TKT-' . strtoupper(Str::random(6));
        $validated['status'] = 'open';
        $validated['user_id'] = auth()->id();

        $ticket = SupportTicket::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully.',
            'data'    => $ticket->load(['client:id,name', 'assignee:id,name']),
        ], 201);
    }

    /**
     * GET /api/tickets/{id} — Ticket details with messages
     */
    public function show(int $id): JsonResponse
    {
        $ticket = SupportTicket::with([
            'client:id,name',
            'assignee:id,name',
            'messages' => fn ($q) => $q->with('sender:id,name')->orderBy('created_at'),
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $ticket,
        ]);
    }

    /**
     * POST /api/tickets/{id}/reply — Add reply
     */
    public function reply(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        $message = $ticket->addMessage(
            $request->message,
            auth()->id(),
            auth()->user()?->isAdmin() ? 'agent' : 'client',
        );

        return response()->json([
            'success' => true,
            'message' => 'Reply sent.',
            'data'    => $message->load('sender:id,name'),
        ], 201);
    }
}
