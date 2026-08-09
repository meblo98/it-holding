<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_create_service_with_image_and_price_and_icon()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('cloud_service.jpg');

        $response = $this->actingAs($this->admin)->post(route('admin.services.store'), [
            'title' => 'Développement Cloud Premium',
            'price' => 150000,
            'icon' => 'cloud',
            'description' => 'Hébergement et architecture cloud sécurisée.',
            'content' => 'Nous concevons et déployons des architectures cloud hautement disponibles.',
            'image' => $file,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $response->assertSessionHas('success');

        $service = Service::first();
        $this->assertNotNull($service);
        $this->assertEquals('Développement Cloud Premium', $service->title);
        $this->assertEquals(150000, $service->price);
        $this->assertEquals('cloud', $service->icon);
        $this->assertEquals('developpement-cloud-premium', $service->slug);
        $this->assertTrue($service->active);

        // Check file exists in fake storage
        Storage::disk('public')->assertExists($service->image);
    }

    public function test_admin_can_update_service_and_replace_image()
    {
        Storage::fake('public');

        // Create initial service
        $initialFile = UploadedFile::fake()->image('old_service.jpg');
        $initialPath = $initialFile->store('services', 'public');

        $service = Service::create([
            'title' => 'Ancien Service',
            'slug' => 'ancien-service',
            'description' => 'Description',
            'content' => 'Contenu',
            'price' => 50000,
            'icon' => 'code',
            'image' => $initialPath,
            'active' => true,
        ]);

        Storage::disk('public')->assertExists($initialPath);

        // Update service with new image and price
        $newFile = UploadedFile::fake()->image('new_service.jpg');

        $response = $this->actingAs($this->admin)->put(route('admin.services.update', $service->id), [
            'title' => 'Nouveau Titre Service',
            'price' => 80000,
            'icon' => 'support',
            'description' => 'Nouvelle description',
            'content' => 'Nouveau contenu',
            'image' => $newFile,
            'active' => 1,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $response->assertSessionHas('success');

        $service->refresh();
        $this->assertEquals('Nouveau Titre Service', $service->title);
        $this->assertEquals(80000, $service->price);
        $this->assertEquals('support', $service->icon);

        // Assert new image was stored and old image was deleted
        Storage::disk('public')->assertExists($service->image);
        Storage::disk('public')->assertMissing($initialPath);
    }

    public function test_service_validation_rules()
    {
        Storage::fake('public');

        // Test file too large (>2MB)
        $largeFile = UploadedFile::fake()->create('large.jpg', 3000); // 3MB

        $response = $this->actingAs($this->admin)->post(route('admin.services.store'), [
            'title' => '', // Required field missing
            'price' => -10, // Invalid negative price
            'description' => 'Short desc',
            'content' => 'Full content',
            'image' => $largeFile,
            'active' => 1,
        ]);

        $response->assertSessionHasErrors(['title', 'price', 'image']);
    }
}
