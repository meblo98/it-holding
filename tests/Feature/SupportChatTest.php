<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SupportChatTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_send_a_message_and_fetch_messages()
    {
        // 1. Guest sends a message
        $response = $this->post(route('chat.send'), [
            'message' => 'Hello Support!',
            'user_name' => 'John Guest',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $cookieName = config('session.cookie');
        $cookieVal = $response->getCookie($cookieName);
        $this->assertNotNull($cookieVal);

        $msg = ChatMessage::latest()->first();
        $this->assertNotNull($msg);

        $this->assertDatabaseHas('chat_messages', [
            'message' => 'Hello Support!',
            'user_name' => 'John Guest',
            'session_id' => $msg->session_id,
            'is_from_admin' => false,
        ]);

        // 2. Fetch messages as guest using the session cookie
        $fetchResponse = $this->withCookie($cookieName, $cookieVal->getValue())
            ->get(route('chat.messages'));
        $fetchResponse->assertStatus(200);
        $fetchResponse->assertJsonFragment([
            'message' => 'Hello Support!',
        ]);
    }

    /** @test */
    public function an_authenticated_user_can_send_a_message()
    {
        $user = User::factory()->create(['name' => 'Alice Client']);

        $response = $this->actingAs($user)->post(route('chat.send'), [
            'message' => 'I have a problem with my order.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'user_name' => 'Alice Client',
            'message' => 'I have a problem with my order.',
            'is_from_admin' => false,
        ]);
    }

    /** @test */
    public function admin_can_view_conversations_list_and_messages()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $client = User::factory()->create(['name' => 'Bob Client']);

        // Create client message
        ChatMessage::create([
            'user_id' => $client->id,
            'user_name' => $client->name,
            'message' => 'Help me please.',
            'is_from_admin' => false,
        ]);

        // View admin dashboard list
        $response = $this->actingAs($admin)->get(route('admin.chat.index'));
        $response->assertStatus(200);
        $response->assertSee('Bob Client');
        $response->assertSee('Help me please.');

        // Get messages for this user
        $messagesResponse = $this->actingAs($admin)->get(route('admin.chat.messages', 'u-' . $client->id));
        $messagesResponse->assertStatus(200);
        $messagesResponse->assertJsonFragment([
            'message' => 'Help me please.',
        ]);
    }

    /** @test */
    public function admin_can_reply_to_a_conversation()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_admin' => true]);
        $client = User::factory()->create(['name' => 'Bob Client']);

        // Create client message
        ChatMessage::create([
            'user_id' => $client->id,
            'user_name' => $client->name,
            'message' => 'Help me please.',
            'is_from_admin' => false,
        ]);

        // Send admin reply
        $response = $this->actingAs($admin)->post(route('admin.chat.send', 'u-' . $client->id), [
            'message' => 'Sure, how can I help?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $client->id,
            'message' => 'Sure, how can I help?',
            'is_from_admin' => true,
        ]);
    }

    /** @test */
    public function it_triggers_gemini_chatbot_response_when_api_key_is_configured()
    {
        // 1. Configure fake api key
        config(['services.gemini.key' => 'test-gemini-key']);

        // 2. Fake HTTP request to Gemini
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Ceci est une réponse générée par l\'IA Gemini.']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $user = User::factory()->create(['name' => 'Alice Client']);

        $response = $this->actingAs($user)->post(route('chat.send'), [
            'message' => 'Bonjour, quel est votre délai de livraison ?',
        ]);

        $response->assertStatus(200);

        // Verify client message was saved
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'message' => 'Bonjour, quel est votre délai de livraison ?',
            'is_from_admin' => false,
        ]);

        // Verify Gemini message was saved
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'user_name' => 'Assistant IA',
            'message' => 'Ceci est une réponse générée par l\'IA Gemini.',
            'is_from_admin' => true,
        ]);
    }

    /** @test */
    public function it_does_not_trigger_gemini_when_api_key_is_missing()
    {
        // Ensure API key is null/empty
        config(['services.gemini.key' => null]);

        $user = User::factory()->create(['name' => 'Alice Client']);

        $response = $this->actingAs($user)->post(route('chat.send'), [
            'message' => 'Bonjour, quel est votre délai de livraison ?',
        ]);

        $response->assertStatus(200);

        // Verify only client message was saved
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'message' => 'Bonjour, quel est votre délai de livraison ?',
            'is_from_admin' => false,
        ]);

        // Verify no Gemini/admin message was auto-saved
        $this->assertDatabaseMissing('chat_messages', [
            'user_id' => $user->id,
            'is_from_admin' => true,
        ]);
    }
}
