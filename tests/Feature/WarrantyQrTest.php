<?php

namespace Tests\Feature;

use App\Models\Warranty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarrantyQrTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $warranty;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@itholding.sn',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create a warranty
        $this->warranty = Warranty::create([
            'number' => 'GAR-2026-0001',
            'product_name' => 'Ordinateur HP EliteBook',
            'serial_number' => 'SN12345678',
            'client_name' => 'Jean Dupont',
            'client_phone' => '771234567',
            'purchase_date' => '2026-01-01',
            'expiry_date' => '2027-01-01',
            'duration_months' => 12,
            'type' => 'standard',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function public_can_verify_warranty_by_number()
    {
        $response = $this->get(route('warranty.verify', $this->warranty->number));

        $response->assertStatus(200);
        $response->assertSee('Garantie Certifiée Active');
        $response->assertSee($this->warranty->number);
        $response->assertSee('Ordinateur HP EliteBook');
    }

    /** @test */
    public function public_can_verify_warranty_by_serial_number()
    {
        $response = $this->get(route('warranty.verify', $this->warranty->serial_number));

        $response->assertStatus(200);
        $response->assertSee('Garantie Certifiée Active');
        $response->assertSee($this->warranty->number);
    }

    /** @test */
    public function public_verification_displays_not_found_on_invalid_number()
    {
        $response = $this->get(route('warranty.verify', 'INVALID-CODE'));

        $response->assertStatus(200);
        $response->assertSee('Garantie Introuvable');
        $response->assertSee('INVALID-CODE');
    }

    /** @test */
    public function public_can_download_qr_code()
    {
        $response = $this->get(route('warranty.qrcode.download', $this->warranty->number));

        // It should either return the raw PNG content (redirect code 302 to API on failure, or 200 with png headers on success)
        $this->assertTrue(in_array($response->status(), [200, 302]));
        
        if ($response->status() === 200) {
            $response->assertHeader('Content-Type', 'image/png');
            $response->assertHeader('Content-Disposition', 'attachment; filename="qrcode-' . $this->warranty->number . '.png"');
        }
    }

    /** @test */
    public function admin_can_access_scanner_page()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.warranties.scanner'));

        $response->assertStatus(200);
        $response->assertSee('Scanner Garantie');
        $response->assertSee('Saisie Manuelle');
    }

    /** @test */
    public function admin_scan_search_redirects_to_warranty_show_on_exact_match()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.warranties.scanSearch') . '?code=' . urlencode($this->warranty->number));

        $response->assertRedirect(route('admin.warranties.show', $this->warranty->id));
    }

    /** @test */
    public function admin_scan_search_redirects_to_warranty_show_on_url_match()
    {
        $scanUrl = route('warranty.verify', $this->warranty->number);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.warranties.scanSearch') . '?code=' . urlencode($scanUrl));

        $response->assertRedirect(route('admin.warranties.show', $this->warranty->id));
    }

    /** @test */
    public function admin_scan_search_redirects_to_index_with_error_on_no_match()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.warranties.scanSearch') . '?code=NOT-EXISTENT');

        $response->assertRedirect(route('admin.warranties.index'));
        $response->assertSessionHas('error');
    }
}
