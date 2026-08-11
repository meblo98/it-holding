<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\PartnerProspect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PartnerCRMTest extends TestCase
{
    use RefreshDatabase;

    protected $partnerUser;
    protected $clientUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create approved partner user
        $this->partnerUser = User::factory()->create([
            'role' => 'partner',
            'partner_status' => 'approved',
            'partner_code' => 'PART-100200',
        ]);

        // Create normal client user
        $this->clientUser = User::factory()->create([
            'role' => 'client',
        ]);
    }

    /** @test */
    public function it_prevents_non_partners_from_accessing_crm_and_assistant()
    {
        // 1. Non-logged in user
        $this->get(route('dashboard.partner.crm'))->assertRedirect(route('login'));
        $this->get(route('dashboard.partner.assistant'))->assertRedirect(route('login'));

        // 2. Logged in non-partner
        $response1 = $this->actingAs($this->clientUser)->get(route('dashboard.partner.crm'));
        $response1->assertStatus(403);

        $response2 = $this->actingAs($this->clientUser)->get(route('dashboard.partner.assistant'));
        $response2->assertStatus(403);
    }

    /** @test */
    public function it_allows_approved_partners_to_access_crm_and_manage_prospects()
    {
        // 1. Access index
        $response = $this->actingAs($this->partnerUser)->get(route('dashboard.partner.crm'));
        $response->assertStatus(200);

        // 2. Create a prospect
        $createResponse = $this->actingAs($this->partnerUser)->post(route('dashboard.partner.crm.store'), [
            'name' => 'Abdou Ndiaye',
            'phone' => '775551122',
            'email' => 'abdou@example.com',
            'company' => 'Ndiaye Express',
            'need' => 'Achat de 5 ordinateurs portables HP EliteBook',
            'budget' => 2500000,
            'status' => 'new',
            'notes' => 'Premier contact par téléphone',
            'next_action_at' => now()->addDays(2)->toDateTimeString(),
            'next_action_description' => 'Rappeler pour envoyer le devis',
        ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('partner_prospects', [
            'partner_id' => $this->partnerUser->id,
            'name' => 'Abdou Ndiaye',
            'company' => 'Ndiaye Express',
            'budget' => 2500000,
            'status' => 'new',
        ]);

        $prospect = PartnerProspect::latest()->first();

        // 3. Update the prospect
        $updateResponse = $this->actingAs($this->partnerUser)->put(route('dashboard.partner.crm.update', $prospect->id), [
            'name' => 'Abdou Ndiaye Modifié',
            'phone' => '775551122',
            'email' => 'abdou@example.com',
            'company' => 'Ndiaye Express',
            'need' => 'Achat de 5 ordinateurs portables HP EliteBook',
            'budget' => 2800000,
            'status' => 'interested',
            'notes' => 'Intéressé par le devis',
            'next_action_at' => now()->addDays(4)->toDateTimeString(),
            'next_action_description' => 'Faire une relance',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('partner_prospects', [
            'id' => $prospect->id,
            'name' => 'Abdou Ndiaye Modifié',
            'budget' => 2800000,
            'status' => 'interested',
        ]);

        // 4. Delete the prospect
        $deleteResponse = $this->actingAs($this->partnerUser)->delete(route('dashboard.partner.crm.destroy', $prospect->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('partner_prospects', [
            'id' => $prospect->id,
        ]);
    }

    /** @test */
    public function it_triggers_gemini_assistant_chat_correctly()
    {
        // Setup fake key
        config(['services.gemini.key' => 'test-gemini-key']);

        // Create product
        $product = Product::create([
            'name' => 'Ordinateur HP EliteBook',
            'slug' => 'hp-elitebook-840',
            'price' => 100000,
            'purchase_price' => 70000,
            'stock' => 10,
            'active' => true,
            'condition' => 'new',
        ]);

        // Fake Gemini response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Voici un argumentaire commercial pour le HP EliteBook :']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Access assistant page
        $this->actingAs($this->partnerUser)->get(route('dashboard.partner.assistant'))->assertStatus(200);

        // Chat call
        $response = $this->actingAs($this->partnerUser)->post(route('dashboard.partner.assistant.chat'), [
            'message' => 'Rédige une pub pour ce pc',
            'product_id' => $product->id,
            'objective' => 'sell',
            'network' => 'whatsapp',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'reply' => 'Voici un argumentaire commercial pour le HP EliteBook :',
        ]);
    }
}
