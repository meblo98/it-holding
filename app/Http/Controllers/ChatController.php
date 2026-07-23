<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
