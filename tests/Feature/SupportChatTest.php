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
        // Start session
        Session::start();
        $sessionId = Session::getId();

        // 1. Guest sends a message
        $response = $this->post(route('chat.send'), [
            'message' => 'Hello Support!',
            'user_name' => 'John Guest',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'message' => 'Hello Support!',
            'user_name' => 'John Guest',
            'session_id' => $sessionId,
            'is_from_admin' => false,
        ]);

        // 2. Fetch messages as guest
        $fetchResponse = $this->get(route('chat.messages'));
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
        $admin = User::factory()->create(['role' => 'admin']);
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
        $admin = User::factory()->create(['role' => 'admin']);
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
}
