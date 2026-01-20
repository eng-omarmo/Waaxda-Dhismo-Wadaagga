<?php

namespace Tests\Feature;

use App\Models\OnlinePayment;
use App\Models\PendingRegistration;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentCallbackTest extends TestCase
{
    public function test_get_success_verifies_and_redirects(): void
    {
        $service = Service::create([
            'name' => 'Callback Service',
            'slug' => 'callback-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 15.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);
        $reg = PendingRegistration::create([
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+252615000000',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);
        $payment = OnlinePayment::create([
            'pending_registration_id' => $reg->id,
            'provider' => 'somx',
            'payment_method' => 'initialize',
            'amount' => $service->price,
            'currency' => 'USD',
            'status' => 'initiated',
            'transaction_id' => 'TX-SUCCESS-123',
            'reference' => 'REF123',
        ]);
        Http::fake([
            'https://pay.somxchange.com/merchant/api/verify' => Http::response([
                'data' => ['access_token' => 'tok_'.Str::random(10)],
            ], 200),
            'https://pay.somxchange.com/merchant/api/verify-transaction/TX-SUCCESS-123' => Http::response([
                'data' => ['status' => 'success', 'responseCode' => '00'],
            ], 200),
        ]);
        $resp = $this->get('/payment/callback/success?transactionId=TX-SUCCESS-123');
        $resp->assertStatus(302);
        $resp->assertSessionHas('success');
        $payment->refresh();
        $this->assertSame('succeeded', $payment->status);
        $this->assertNotNull($payment->verified_at);
    }

    public function test_post_success_verifies_and_redirects(): void
    {
        $service = Service::create([
            'name' => 'Callback Service',
            'slug' => 'callback-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 18.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);
        $reg = PendingRegistration::create([
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+252615000000',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);
        $payment = OnlinePayment::create([
            'pending_registration_id' => $reg->id,
            'provider' => 'somx',
            'payment_method' => 'initialize',
            'amount' => $service->price,
            'currency' => 'USD',
            'status' => 'initiated',
            'transaction_id' => 'TX-SUCCESS-POST',
            'reference' => 'REFPOST',
        ]);
        Http::fake([
            'https://pay.somxchange.com/merchant/api/verify' => Http::response([
                'data' => ['access_token' => 'tok_'.Str::random(10)],
            ], 200),
            'https://pay.somxchange.com/merchant/api/verify-transaction/TX-SUCCESS-POST' => Http::response([
                'data' => ['status' => 'approved', 'responseCode' => '00'],
            ], 200),
        ]);
        $resp = $this->post('/payment/callback/success', ['transactionId' => 'TX-SUCCESS-POST']);
        $resp->assertStatus(302);
        $payment->refresh();
        $this->assertSame('succeeded', $payment->status);
        $this->assertNotNull($payment->verified_at);
    }

    public function test_get_failure_displays_page_and_marks_failed(): void
    {
        $service = Service::create([
            'name' => 'Callback Service',
            'slug' => 'callback-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 10.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);
        $reg = PendingRegistration::create([
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+252615000000',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);
        $payment = OnlinePayment::create([
            'pending_registration_id' => $reg->id,
            'provider' => 'somx',
            'payment_method' => 'initialize',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'initiated',
            'transaction_id' => 'TX-FAIL-GET',
            'reference' => 'REFFAIL',
        ]);
        $resp = $this->get('/payment/callback/failure?transactionId=TX-FAIL-GET&message=Cancelled');
        $resp->assertStatus(400);
        $resp->assertSee('Payment Failed');
        $resp->assertSee('Cancelled');
        $payment->refresh();
        $this->assertSame('failed', $payment->status);
    }

    public function test_post_failure_displays_page_and_marks_failed(): void
    {
        $service = Service::create([
            'name' => 'Callback Service',
            'slug' => 'callback-service-'.Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 10.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);
        $reg = PendingRegistration::create([
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+252615000000',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);
        $payment = OnlinePayment::create([
            'pending_registration_id' => $reg->id,
            'provider' => 'somx',
            'payment_method' => 'initialize',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'initiated',
            'transaction_id' => 'TX-FAIL-POST',
            'reference' => 'REFFAILP',
        ]);
        $resp = $this->post('/payment/callback/failure', ['transactionId' => 'TX-FAIL-POST', 'message' => 'Declined']);
        $resp->assertStatus(400);
        $resp->assertSee('Declined');
        $payment->refresh();
        $this->assertSame('failed', $payment->status);
    }
}
