<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Warranty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_access_ticket_pages()
    {
        $this->get(route('dashboard.tickets'))->assertRedirect(route('login'));
        $this->get(route('dashboard.tickets.create'))->assertRedirect(route('login'));
        $this->get(route('dashboard.tickets.show', 1))->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authenticated_client_can_view_their_empty_tickets_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.tickets'));

        $response->assertStatus(200);
        $response->assertSee("Support Technique");
        $response->assertSee("aucune demande");
    }

    /** @test */
    public function an_authenticated_client_can_view_their_tickets_list()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '770000000',
        ]);

        $ticket = Ticket::create([
            'number' => 'SAV-2026-0001',
            'client_id' => $client->id,
            'client_name' => $client->full_name,
            'title' => 'My Screen is Broken',
            'description' => 'It displays vertical green lines.',
            'status' => 'open',
            'priority' => 'high',
            'type' => 'repair',
            'opened_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.tickets'));

        $response->assertStatus(200);
        $response->assertSee('SAV-2026-0001');
        $response->assertSee('My Screen is Broken');
        $response->assertSee('Réparation');
    }

    /** @test */
    public function an_authenticated_client_can_view_ticket_creation_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.tickets.create'));

        $response->assertStatus(200);
        $response->assertSee("Ouvrir un nouveau ticket");
    }

    /** @test */
    public function an_authenticated_client_can_submit_a_ticket_successfully_without_attachments()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '770000000',
        ]);
        
        $response = $this->actingAs($user)->post(route('dashboard.tickets.store'), [
            'title' => 'Installation assistance needed',
            'description' => 'I bought a smart lock but I need assistance to install it on my main door.',
            'type' => 'installation',
            'priority' => 'normal',
        ]);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Installation assistance needed',
            'type' => 'installation',
            'priority' => 'normal',
        ]);

        $ticket = Ticket::where('title', 'Installation assistance needed')->first();
        $response->assertRedirect(route('dashboard.tickets.show', $ticket->id));
    }

    /** @test */
    public function an_authenticated_client_can_submit_a_ticket_with_attachments()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '770000000',
        ]);

        $file = UploadedFile::fake()->image('broken_screen.png');

        $response = $this->actingAs($user)->post(route('dashboard.tickets.store'), [
            'title' => 'Broken screen with image',
            'description' => 'Please see the attached photo of the screen lines.',
            'type' => 'repair',
            'priority' => 'high',
            'attachments' => [$file],
        ]);

        $ticket = Ticket::where('title', 'Broken screen with image')->first();
        $this->assertNotNull($ticket);

        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $ticket->id,
            'type' => 'image',
        ]);

        $attachment = $ticket->attachments->first();
        Storage::disk('public')->assertExists($attachment->file_path);
    }

    /** @test */
    public function an_authenticated_client_can_view_their_own_ticket_details()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'user_id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => $user->email,
            'phone' => '770000000',
        ]);

        $ticket = Ticket::create([
            'number' => 'SAV-2026-0002',
            'client_id' => $client->id,
            'client_name' => $client->full_name,
            'title' => 'Maintenance plan request',
            'description' => 'I would like to set up a regular maintenance schedule.',
            'status' => 'open',
            'priority' => 'low',
            'type' => 'maintenance',
            'opened_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.tickets.show', $ticket->id));

        $response->assertStatus(200);
        $response->assertSee('SAV-2026-0002');
        $response->assertSee('Maintenance plan request');
        $response->assertSee('Maintenance');
    }

    /** @test */
    public function an_authenticated_client_cannot_view_someone_elses_ticket()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $client2 = Client::create([
            'user_id' => $user2->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => $user2->email,
            'phone' => '770000000',
        ]);

        $ticket = Ticket::create([
            'number' => 'SAV-2026-0003',
            'client_id' => $client2->id,
            'client_name' => $client2->full_name,
            'title' => 'Confidential support issue',
            'description' => 'Only for user 2.',
            'status' => 'open',
            'priority' => 'low',
            'type' => 'advice',
            'opened_at' => now(),
        ]);

        $response = $this->actingAs($user1)->get(route('dashboard.tickets.show', $ticket->id));

        $response->assertStatus(403);
    }
}
