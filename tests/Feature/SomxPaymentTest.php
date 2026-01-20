<?php

namespace Tests\Feature;

use App\Models\PendingRegistration;
use App\Models\Service;
use App\Models\OnlinePayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class SomxPaymentTest extends TestCase
{
    public function test_service_create_transaction_returns_approved_url(): void
    {
        $approvedUrl = 'https://gateway.example/approved';
        Http::fake(function ($request) use ($approvedUrl) {
            $url = $request->url();
            if (str_contains($url, '/merchant/api/verify')) {
                return Http::response([
                    'data' => ['access_token' => 'tok_' . Str::random(10)],
                ], 200);
            }
            if (str_contains($url, '/merchant/api/transaction-info')) {
                return Http::response([
                    'data' => ['approvedUrl' => $approvedUrl, 'transactionId' => 'ABC123'],
                ], 200);
            }
            return Http::response(['message' => 'not found'], 404);
        });
        $payload = [
            'phone' => '+252615000000',
            'amount' => 10.50,
            'currency' => 'USD',
            'successUrl' => 'https://marhabahotel.so/online-ordering/',
            'cancelUrl' => 'https://marhabahotel.so/',
            'order_info' => ['item_name' => 'service', 'order_no' => 'ORD123'],
        ];
        $svc = new \App\Services\PaymentService();
        $result = $svc->createTransaction($payload);
        $this->assertSame($approvedUrl, $result['approved_url']);
    }
    public function test_initialize_redirects_to_gateway_on_success(): void
    {
        $service = Service::create([
            'name' => 'Somx Service',
            'slug' => 'somx-service-' . Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 10.50,
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
        $approvedUrl = 'https://gateway.example/approved';
        Http::fake(function ($request) use ($approvedUrl) {
            $url = $request->url();
            if (str_contains($url, '/merchant/api/verify')) {
                return Http::response([
                    'data' => ['access_token' => 'tok_' . Str::random(10)],
                ], 200);
            }
            if (str_contains($url, '/merchant/api/transaction-info')) {
                return Http::response([
                    'data' => ['approvedUrl' => $approvedUrl, 'transactionId' => 'ABC123'],
                ], 200);
            }
            return Http::response(['message' => 'not found'], 404);
        });

        $response = $this
            ->withSession(['portal_reg_id' => $reg->id])
            ->post('/portal/pay', ['payment_method' => 'initialize']);
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $payment = OnlinePayment::where('pending_registration_id', $reg->id)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertSame('somx', $payment->provider);
        $this->assertSame('USD', $payment->currency);
        $this->assertSame('initiated', $payment->status);
        $this->assertSame(10.50, (float) $payment->amount);
    }

    public function test_initialize_fails_with_invalid_amount(): void
    {
        $service = Service::create([
            'name' => 'Somx Service',
            'slug' => 'somx-service-' . Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 0.00,
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
        Http::fake([
            'https://pay.somxchange.com/merchant/api/verify' => Http::response([
                'data' => ['access_token' => 'tok_' . Str::random(10)],
            ], 200),
            'https://pay.somxchange.com/merchant/api/transaction-info' => Http::response([
                'message' => 'Invalid amount',
            ], 400),
        ]);
        $response = $this
            ->withSession(['portal_reg_id' => $reg->id])
            ->post('/portal/pay', ['payment_method' => 'initialize']);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['payment']);
        $payment = OnlinePayment::where('pending_registration_id', $reg->id)->latest()->first();
        $this->assertNull($payment);
    }

    public function test_initialize_fails_with_invalid_phone(): void
    {
        $service = Service::create([
            'name' => 'Somx Service',
            'slug' => 'somx-service-' . Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 5.00,
            'icon_color' => 'bg-primary',
            'icon_class' => 'bi-gear',
        ]);
        $reg = PendingRegistration::create([
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '615000000',
            'status' => 'draft',
            'step' => 5,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);
        $response = $this
            ->withSession(['portal_reg_id' => $reg->id])
            ->post('/portal/pay', ['payment_method' => 'initialize']);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['phone']);
        $payment = OnlinePayment::where('pending_registration_id', $reg->id)->latest()->first();
        $this->assertNull($payment);
    }

    public function test_cancelation_scenario_returns_error(): void
    {
        $service = Service::create([
            'name' => 'Somx Service',
            'slug' => 'somx-service-' . Str::lower(Str::random(6)),
            'description' => 'Desc',
            'price' => 8.00,
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
        Http::fake([
            'https://pay.somxchange.com/merchant/api/verify' => Http::response([
                'data' => ['access_token' => 'tok_' . Str::random(10)],
            ], 200),
            'https://pay.somxchange.com/merchant/api/transaction-info' => Http::response([
                'message' => 'Cancelled',
            ], 400),
        ]);
        $response = $this
            ->withSession(['portal_reg_id' => $reg->id])
            ->post('/portal/pay', ['payment_method' => 'initialize']);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['payment']);
        $payment = OnlinePayment::where('pending_registration_id', $reg->id)->latest()->first();
        $this->assertNull($payment);
    }
}
