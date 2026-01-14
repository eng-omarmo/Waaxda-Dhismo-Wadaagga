<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class SelfServiceServiceIdTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('database.default', 'sqlite');
        $path = base_path('database/database.sqlite');
        if (! file_exists($path)) {
            @mkdir(dirname($path), 0777, true);
            @touch($path);
        }
        Config::set('database.connections.sqlite.database', $path);
    }

    public function test_start_with_valid_service_id_redirects_to_info(): void
    {
        $service = Service::create([
            'name' => 'Test Service',
            'slug' => 'test-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 10.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);

        $response = $this->get('/portal?serviceId='.$service->id);
        $response->assertStatus(302);
        $response->assertRedirect('/portal/info');
    }

    public function test_start_with_missing_service_id_renders_select_service(): void
    {
        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Choose a service');
    }

    public function test_start_with_malformed_service_id_returns_400(): void
    {
        $response = $this->get('/portal?serviceId=abc');
        $response->assertStatus(400);
    }

    public function test_store_with_valid_service_id_redirects_to_info(): void
    {
        $service = Service::create([
            'name' => 'Test Service 2',
            'slug' => 'test-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 20.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);

        $response = $this->post('/portal/service', ['serviceId' => $service->id]);
        $response->assertStatus(302);
        $response->assertRedirect('/portal/info');
    }

    public function test_store_with_missing_service_id_returns_400(): void
    {
        $response = $this->post('/portal/service', []);
        $response->assertStatus(400);
    }

    public function test_store_with_malformed_service_id_returns_400(): void
    {
        $response = $this->post('/portal/service', ['serviceId' => 'xyz']);
        $response->assertStatus(400);
    }

    public function test_pay_requires_service_id_when_registration_missing_service(): void
    {
        $reg = \App\Models\PendingRegistration::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) \Illuminate\Support\Str::uuid(),
            'data' => [],
        ]);

        $response = $this->withSession(['portal_reg_id' => $reg->id])->get('/portal/pay');
        $response->assertStatus(400);
    }

    public function test_pay_with_service_id_sets_registration_and_renders(): void
    {
        $service = Service::create([
            'name' => 'Pay Service',
            'slug' => 'pay-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 30.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);
        $reg = \App\Models\PendingRegistration::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) \Illuminate\Support\Str::uuid(),
            'data' => [],
        ]);
        $response = $this->withSession(['portal_reg_id' => $reg->id])->get('/portal/pay?serviceId='.$service->id);
        $response->assertStatus(200);
        $response->assertSee('Amount due');
    }
}
