<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ContactMessageTest extends TestCase
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
        Artisan::call('migrate:fresh', ['--force' => true]);
    }

    public function test_public_contact_store_creates_record(): void
    {
        $response = $this->post('/contact', [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '700000000',
            'service_type' => 'Cabasho / Mid kale',
            'message' => 'Hello',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('contact_messages', [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '700000000',
        ]);
    }

    public function test_admin_contacts_index_lists_messages(): void
    {
        ContactMessage::create([
            'full_name' => 'Alice',
            'email' => 'alice@example.com',
            'phone' => '700000001',
            'service_type' => 'Warqadda Lahaanshaha',
            'message' => 'Inquiry',
        ]);
        $admin = User::create([
            'email' => 'admin@example.com',
            'password' => 'Password@123',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'contact_phone' => '700000009',
            'contact_address' => 'Admin Street',
            'role' => 'admin',
            'active' => true,
        ]);
        $response = $this->actingAs($admin)->get('/admin/contacts');
        $response->assertStatus(200);
        $response->assertSee('Alice');
        $response->assertSee('alice@example.com');
    }
}
