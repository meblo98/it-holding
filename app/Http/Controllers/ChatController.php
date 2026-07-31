<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Get messages for the current client (frontend widget)
     */
    public function getMessages()
    {
        $query = ChatMessage::query();

        if (Auth::check()) {
            $userId = Auth::id();
            $query->where('user_id', $userId);
        } else {
            $sessionId = Session::getId();
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        // Mark admin messages as read
        $query->where('is_from_admin', true)->where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_from_admin' => $msg->is_from_admin,
                    'time' => $msg->created_at->format('H:i'),
                ];
            })
        ]);
    }

    /**
     * Send a message from the client (frontend widget)
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'user_name' => 'nullable|string|max:100',
        ]);

        $data = [
            'message' => $request->message,
            'is_from_admin' => false,
            'is_read' => false,
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
            $data['user_name'] = Auth::user()->name;
        } else {
            $data['session_id'] = Session::getId();
            $data['user_name'] = $request->input('user_name') ?: 'Visiteur';
        }

        $msg = ChatMessage::create($data);

        // Generate AI reply if API key is present
        $aiReply = $this->getGeminiReply($request->message, $msg->user_id, $msg->session_id);
        if ($aiReply) {
            ChatMessage::create([
                'user_id' => $msg->user_id,
                'session_id' => $msg->session_id,
                'user_name' => 'Assistant IA',
                'message' => $aiReply,
                'is_from_admin' => true,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_from_admin' => false,
                'time' => $msg->created_at->format('H:i'),
            ]
        ]);
    }

    /**
     * Get reply from Gemini API using the conversation context
     */
    private function getGeminiReply($clientMessage, $userId = null, $sessionId = null)
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return null;
        }

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Retrieve last 10 messages for context (excluding the message just saved)
        $query = ChatMessage::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        // We only fetch history prior to the newest message to build context cleanly
        $recentMessages = $query->orderBy('created_at', 'desc')->skip(1)->take(10)->get()->reverse();

        $context = "Tu es l'assistant IA de support client pour IT-HOLDING, une entreprise technologique de référence au Sénégal spécialisée dans les équipements informatiques (ordinateurs, composants PC, réseaux, serveurs), les services informatiques, et les solutions SaaS/logiciels.\n";
        $context .= "Réponds poliment, professionnellement et de manière concise en français. Aide le client en fonction de l'historique ci-dessous.\n\n";
        $context .= "Historique de la discussion :\n";

        foreach ($recentMessages as $msg) {
            $sender = $msg->is_from_admin ? "Support IT-HOLDING" : ($msg->user_name ?: "Client");
            $context .= "- {$sender}: {$msg->message}\n";
        }
        $context .= "- Client (nouveau message): {$clientMessage}\n";
        $context .= "Support IT-HOLDING :";

        try {
            $response = Http::timeout(20)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $context]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                return $reply ? trim($reply) : null;
            } else {
                Log::error('Gemini API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Admin view of all conversations
     */
    public function adminIndex()
    {
        // Get all unique conversations grouped by user or session
        // We get the latest message for each group
        $rawConversations = ChatMessage::orderBy('created_at', 'desc')->get();

        $conversations = [];
        $processedKeys = [];

        foreach ($rawConversations as $msg) {
            $key = $msg->user_id ? 'u-' . $msg->user_id : 's-' . $msg->session_id;
            
            if (in_array($key, $processedKeys)) {
                continue;
            }

            $processedKeys[] = $key;

            // Count unread messages from client
            $unreadCount = ChatMessage::where('is_from_admin', false)
                ->where('is_read', false)
                ->where(function ($q) use ($msg) {
                    if ($msg->user_id) {
                        $q->where('user_id', $msg->user_id);
                    } else {
                        $q->where('session_id', $msg->session_id)->whereNull('user_id');
                    }
                })
                ->count();

            $conversations[] = [
                'key' => $key,
                'user_name' => $msg->user_name ?: ($msg->user ? $msg->user->name : 'Visiteur'),
                'latest_message' => $msg->message,
                'unread_count' => $unreadCount,
                'time' => $msg->created_at->diffForHumans(),
                'updated_at' => $msg->created_at,
            ];
        }

        // Sort by updated_at desc
        usort($conversations, function ($a, $b) {
            return $b['updated_at'] <=> $a['updated_at'];
        });

        return view('admin.chat.index', compact('conversations'));
    }

    /**
     * Get messages for a specific conversation in admin
     */
    public function adminGetMessages($identifier)
    {
        $query = ChatMessage::query();
        $this->applyIdentifierFilter($query, $identifier);

        $messages = $query->orderBy('created_at', 'asc')->get();

        // Mark client messages as read
        $query2 = ChatMessage::query();
        $this->applyIdentifierFilter($query2, $identifier);
        $query2->where('is_from_admin', false)->where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_from_admin' => $msg->is_from_admin,
                    'time' => $msg->created_at->format('H:i'),
                ];
            })
        ]);
    }

    /**
     * Send a message from admin to client
     */
    public function adminSendMessage(Request $request, $identifier)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $data = [
            'message' => $request->message,
            'is_from_admin' => true,
            'is_read' => false,
        ];

        if (str_starts_with($identifier, 'u-')) {
            $userId = substr($identifier, 2);
            $data['user_id'] = $userId;
            $user = User::find($userId);
            $data['user_name'] = $user ? $user->name : 'Client';
        } else {
            $sessionId = substr($identifier, 2);
            $data['session_id'] = $sessionId;
            
            // Find the last client message in this session to get their user name
            $lastMsg = ChatMessage::where('session_id', $sessionId)
                ->where('is_from_admin', false)
                ->orderBy('created_at', 'desc')
                ->first();
            $data['user_name'] = $lastMsg ? $lastMsg->user_name : 'Visiteur';
        }

        $msg = ChatMessage::create($data);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_from_admin' => true,
                'time' => $msg->created_at->format('H:i'),
            ]
        ]);
    }

    /**
     * Helper to apply identifier filter on query
     */
    private function applyIdentifierFilter($query, $identifier)
    {
        if (str_starts_with($identifier, 'u-')) {
            $userId = substr($identifier, 2);
            $query->where('user_id', $userId);
        } else {
            $sessionId = substr($identifier, 2);
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }
    }
}
