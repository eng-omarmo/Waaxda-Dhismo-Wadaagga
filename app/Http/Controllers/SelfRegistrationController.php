<?php

namespace App\Http\Controllers;

use App\Mail\SelfRegistrationCompleted;
use App\Models\OnlinePayment;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SelfRegistrationController extends Controller
{
    public function start()
    {
        return view('register.step1');
    }

    public function storeStep1(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $reg = PendingRegistration::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'draft',
            'step' => 2,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);

        $request->session()->put('registration_id', $reg->id);

        return redirect()->route('register.step2');
    }

    public function step2(Request $request)
    {
        $reg = $this->current($request);
        $amount = (float) env('REGISTRATION_FEE', 25.00);

        return view('register.step2', compact('reg', 'amount'));
    }

    public function processPayment(Request $request)
    {
        $reg = $this->current($request);
        $amount = (float) env('REGISTRATION_FEE', 25.00);

        $validated = $request->validate([
            'payment_method' => ['required', 'in:card,paypal'],
            'card_name' => ['required_if:payment_method,card', 'string', 'max:255'],
            'card_number' => ['required_if:payment_method,card', 'string', 'min:12', 'max:19'],
            'card_expiry' => ['required_if:payment_method,card', 'string', 'regex:/^(0[1-9]|1[0-2])\\/\\d{2}$/'],
            'card_cvc' => ['required_if:payment_method,card', 'string', 'min:3', 'max:4'],
        ], [
            'card_expiry.regex' => 'Expiry must be MM/YY',
        ]);

        $provider = $validated['payment_method'] === 'paypal' ? 'paypal' : 'fake';
        $status = 'succeeded';
        $transactionId = 'txn_'.Str::random(12);

        $payment = OnlinePayment::create([
            'pending_registration_id' => $reg->id,
            'provider' => $provider,
            'payment_method' => $validated['payment_method'],
            'amount' => $amount,
            'currency' => 'USD',
            'status' => $status,
            'transaction_id' => $transactionId,
            'reference' => Str::upper(Str::random(8)),
            'receipt_number' => 'IPAMS-ONL-'.str_pad((string) $reg->id, 6, '0', STR_PAD_LEFT),
            'verified_at' => now(),
            'metadata' => ['masked_card' => isset($validated['card_number']) ? substr($validated['card_number'], -4) : null],
        ]);

        $reg->status = 'paid';
        $reg->step = 3;
        $reg->save();

        $password = Str::random(12);
        $user = User::create([
            'email' => $reg->email,
            'password' => $password,
            'first_name' => explode(' ', trim($reg->full_name))[0] ?? $reg->full_name,
            'last_name' => trim(Str::after($reg->full_name, $reg->first_name ?? '')) ?: '',
            'contact_phone' => $reg->phone ?: '',
            'role' => 'user',
            'active' => true,
        ]);

        Auth::login($user);

        $receiptUrl = URL::temporarySignedRoute('receipt.online.show', now()->addDays(7), ['payment' => $payment->id]);
        try {
            Mail::to($user->email)->send(new SelfRegistrationCompleted($user, $payment, $password, $receiptUrl));
        } catch (\Throwable $e) {
        }

        return redirect()->route('dashboard')->with('status', 'Registration successful');
    }

    public function resume(Request $request, string $token)
    {
        $reg = PendingRegistration::where('resume_token', $token)->firstOrFail();
        $request->session()->put('registration_id', $reg->id);

        return redirect()->route($reg->step === 2 ? 'register.step2' : 'register.step1');
    }

    public function publicReceiptOnline(OnlinePayment $payment)
    {
        $reg = PendingRegistration::findOrFail($payment->pending_registration_id);

        return view('receipt-online', ['payment' => $payment, 'registration' => $reg]);
    }

    private function current(Request $request): PendingRegistration
    {
        $id = $request->session()->get('registration_id');
        abort_unless($id, 404);

        return PendingRegistration::findOrFail($id);
    }
}
