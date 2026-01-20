<?php

namespace Tests\Feature;

use App\Models\BusinessLicense;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminServiceRequestReflectionTest extends TestCase
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
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
    }

    private function makeAdmin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'password' => 'Password@123',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'contact_phone' => '700000009',
                'contact_address' => 'Admin Street',
                'role' => 'admin',
                'active' => true,
            ]
        );
    }

    public function test_project_creation_registers_service_request(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        $service = Service::create([
            'name' => 'Project Registration',
            'slug' => 'project-registration',
            'description' => 'Register projects',
            'price' => 0.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-building',
        ]);

        $resp = $this->post(route('admin.projects.store'), [
            'registrant_name' => 'Alice',
            'registrant_phone' => '700000000',
            'registrant_email' => 'alice@example.com',
            'project_name' => 'Test Tower',
            'location_text' => 'District 1',
            'developer_id' => null,
            'status' => 'Approved',
        ]);
        $resp->assertStatus(302);

        $sr = ServiceRequest::where('service_id', $service->id)
            ->where('user_email', 'alice@example.com')->first();
        $this->assertNotNull($sr, 'ServiceRequest not created for project');
        $this->assertSame('verified', (string) $sr->status);
        $this->assertIsArray($sr->request_details);
        $this->assertNotEmpty($sr->request_details['form_values'] ?? []);
    }

    public function test_organization_creation_registers_service_request(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        $service = Service::create([
            'name' => 'Developer Registration',
            'slug' => 'developer-registration',
            'description' => 'Register organizations',
            'price' => 0.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-people',
        ]);

        $resp = $this->post(route('admin.organizations.store'), [
            'name' => 'Org Inc',
            'registration_number' => 'REG-001',
            'address' => 'City Center',
            'type' => 'Developer',
            'contact_full_name' => 'Bob',
            'contact_role' => 'Manager',
            'contact_phone' => '700000001',
            'contact_email' => 'org@example.com',
            'status' => 'approved',
        ]);
        $resp->assertStatus(302);

        $sr = ServiceRequest::where('service_id', $service->id)
            ->where('user_email', 'org@example.com')->first();
        $this->assertNotNull($sr, 'ServiceRequest not created for organization');
        $this->assertSame('verified', (string) $sr->status);
        $this->assertIsArray($sr->request_details);
        $this->assertNotEmpty($sr->request_details['form_values'] ?? []);
    }

    public function test_business_license_save_registers_service_request(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        $service = Service::create([
            'name' => 'Business License',
            'slug' => 'business-license',
            'description' => 'Issue licenses',
            'price' => 0.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-briefcase',
        ]);

        $license = BusinessLicense::create([
            'company_name' => 'Biz Co',
            'project_id' => null,
            'license_type' => 'Commercial',
            'status' => 'pending',
            'verification_status' => 'unverified',
            'expires_at' => null,
            'admin_comments' => '',
            'registrant_name' => 'Carol',
            'registrant_email' => 'carol@example.com',
            'registrant_phone' => '700000002',
        ]);

        $resp = $this->put(route('admin.licensing.save', $license), [
            'company_name' => 'Biz Co',
            'project_id' => null,
            'license_type' => 'Commercial',
            'status' => 'approved',
            'verification_status' => 'verified',
            'expires_at' => null,
            'admin_comments' => 'OK',
            'registrant_name' => 'Carol',
            'registrant_email' => 'carol@example.com',
            'registrant_phone' => '700000002',
        ]);
        $resp->assertStatus(302);

        $sr = ServiceRequest::where('service_id', $service->id)
            ->where('user_email', 'carol@example.com')->first();
        $this->assertNotNull($sr, 'ServiceRequest not created for business license');
        $this->assertSame('verified', (string) $sr->status);
        $this->assertIsArray($sr->request_details);
        $this->assertSame('Commercial', (string) ($sr->request_details['license_type'] ?? ''));
    }
}
