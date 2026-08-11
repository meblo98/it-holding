<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\MarketingAsset;
use App\Models\PartnerScheduledPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PartnerMarketingTest extends TestCase
{
    use RefreshDatabase;

    protected $partnerUser;
    protected $clientUser;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create approved partner
        $this->partnerUser = User::factory()->create([
            'role' => 'partner',
            'partner_status' => 'approved',
            'partner_code' => 'PART-200300',
        ]);

        // Create client
        $this->clientUser = User::factory()->create([
            'role' => 'client',
        ]);

        // Create admin
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    /** @test */
    public function it_restricts_marketing_studio_to_approved_partners()
    {
        // 1. Guest redirect
        $this->get(route('dashboard.partner.marketing'))->assertRedirect(route('login'));

        // 2. Client forbid
        $this->actingAs($this->clientUser)->get(route('dashboard.partner.marketing'))->assertStatus(403);

        // 3. Approved partner allow
        $this->actingAs($this->partnerUser)->get(route('dashboard.partner.marketing'))->assertStatus(200);
    }

    /** @test */
    public function it_allows_approved_partners_to_generate_catalog_pdf()
    {
        // Create products
        $p1 = Product::create([
            'name' => 'Ordinateur Dell Inspiron',
            'slug' => 'dell-inspiron',
            'price' => 500000,
            'purchase_price' => 400000,
            'stock' => 5,
            'active' => true,
            'condition' => 'new',
        ]);

        $p2 = Product::create([
            'name' => 'Routeur Cisco',
            'slug' => 'routeur-cisco',
            'price' => 150000,
            'purchase_price' => 100000,
            'stock' => 3,
            'active' => true,
            'condition' => 'new',
        ]);

        // Generate catalog
        $response = $this->actingAs($this->partnerUser)->post(route('dashboard.partner.marketing.catalog'), [
            'product_ids' => [$p1->id, $p2->id],
            'catalog_title' => 'Mon Super Catalogue',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function it_allows_admins_to_manage_marketing_assets()
    {
        // Guests cannot access admin marketing assets
        $this->get(route('admin.marketing-assets.index'))->assertRedirect(route('login'));

        // Clients cannot access
        $this->actingAs($this->clientUser)->get(route('admin.marketing-assets.index'))->assertStatus(403);

        // Admin can access
        $this->actingAs($this->adminUser)->get(route('admin.marketing-assets.index'))->assertStatus(200);
    }

    /** @test */
    public function it_allows_partners_to_schedule_and_manage_posts()
    {
        $product = Product::create([
            'name' => 'PC Portable Dell',
            'slug' => 'pc-dell',
            'price' => 400000,
            'purchase_price' => 350000,
            'stock' => 5,
            'active' => true,
            'condition' => 'new',
        ]);

        // 1. Create scheduled post
        $postData = [
            'title' => 'Mon Super Post',
            'content' => 'Achetez ce superbe Dell avec mon lien !',
            'platforms' => ['facebook', 'whatsapp'],
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'product_id' => $product->id,
        ];

        $response = $this->actingAs($this->partnerUser)
            ->post(route('dashboard.partner.marketing.posts.store'), $postData);

        $response->assertRedirect(route('dashboard.partner.marketing'));
        $this->assertDatabaseHas('partner_scheduled_posts', [
            'title' => 'Mon Super Post',
            'status' => 'pending',
        ]);

        $post = PartnerScheduledPost::first();

        // 2. Publish post
        $responsePublish = $this->actingAs($this->partnerUser)
            ->post(route('dashboard.partner.marketing.posts.publish', $post->id));
        
        $responsePublish->assertRedirect(route('dashboard.partner.marketing'));
        $this->assertDatabaseHas('partner_scheduled_posts', [
            'id' => $post->id,
            'status' => 'published',
        ]);

        // 3. Delete post
        $responseDelete = $this->actingAs($this->partnerUser)
            ->delete(route('dashboard.partner.marketing.posts.destroy', $post->id));

        $responseDelete->assertRedirect(route('dashboard.partner.marketing'));
        $this->assertDatabaseMissing('partner_scheduled_posts', ['id' => $post->id]);
    }

    /** @test */
    public function it_allows_partners_to_generate_video_script_via_gemini()
    {
        config(['services.gemini.key' => 'test-api-key']);

        $product = Product::create([
            'name' => 'PC Dell Vostro',
            'slug' => 'dell-vostro',
            'price' => 300000,
            'purchase_price' => 250000,
            'stock' => 5,
            'active' => true,
            'condition' => 'new',
        ]);

        // Mock Gemini response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '
                                {
                                  "scenes": [
                                    {
                                      "num": 1,
                                      "visual": "Montrer le PC de face",
                                      "voiceover": "Découvrez le Dell Vostro !"
                                    }
                                  ]
                                }
                                ']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->partnerUser)
            ->post(route('dashboard.partner.marketing.video'), [
                'product_id' => $product->id,
                'tone' => 'energetic',
                'duration' => 15,
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('scenes.0.voiceover', 'Découvrez le Dell Vostro !');
    }
}
