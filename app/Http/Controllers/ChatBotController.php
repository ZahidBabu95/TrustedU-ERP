<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\ChatBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatBotController extends Controller
{
    public function __construct(
        private ChatBotService $chatBot
    ) {}

    /**
     * Start or resume a chat conversation.
     */
    public function startConversation(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id') ?: Str::uuid()->toString();

        $conversation = ChatConversation::firstOrCreate(
            ['session_id' => $sessionId, 'status' => 'active'],
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_message_at' => now(),
            ]
        );

        // Get recent messages if resuming
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get(['role', 'message', 'created_at'])
            ->map(fn ($m) => [
                'role' => $m->role,
                'message' => $m->message,
                'time' => $m->created_at->format('h:i A'),
            ]);

        return response()->json([
            'session_id' => $sessionId,
            'conversation_id' => $conversation->id,
            'messages' => $messages,
            'greeting' => $messages->isEmpty()
                ? "আসসালামু আলাইকুম! 👋 TrustedU ERP-তে স্বাগতম।\n\nআমি আপনার AI সহকারী। আমাকে যেকোনো প্রশ্ন করতে পারেন:\n\n🏫 প্ল্যাটফর্ম সম্পর্কে\n📋 মডিউল ও ফিচার\n💰 প্রাইসিং\n📞 যোগাযোগ\n🎯 ডেমো বুকিং"
                : null,
        ]);
    }

    /**
     * Send a message and get AI response.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $conversation = ChatConversation::where('session_id', $request->session_id)
            ->where('status', 'active')
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // Save user message
        $userMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'message' => $request->message,
        ]);

        // Get conversation history for context
        $history = $conversation->messages()
            ->orderBy('created_at')
            ->get(['role', 'message'])
            ->toArray();

        // Get AI response
        $botResponse = $this->chatBot->getAIResponse($request->message, $history);

        // Save bot response
        $botMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'bot',
            'message' => $botResponse,
        ]);

        // Update conversation
        $conversation->update([
            'message_count' => $conversation->messages()->count(),
            'last_message_at' => now(),
        ]);

        return response()->json([
            'user_message' => [
                'role' => 'user',
                'message' => $request->message,
                'time' => $userMsg->created_at->format('h:i A'),
            ],
            'bot_response' => [
                'role' => 'bot',
                'message' => $botResponse,
                'time' => $botMsg->created_at->format('h:i A'),
            ],
        ]);
    }

    /**
     * Update visitor info (name, email, phone).
     */
    public function updateVisitorInfo(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $conversation = ChatConversation::where('session_id', $request->session_id)->first();

        if ($conversation) {
            $conversation->update(array_filter([
                'visitor_name' => $request->name,
                'visitor_email' => $request->email,
                'visitor_phone' => $request->phone,
            ]));
        }

        return response()->json(['status' => 'updated']);
    }
}
