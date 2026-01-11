<?php

namespace App\Mail;

use App\Models\OnlinePayment;
use App\Models\PendingRegistration;
use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SelfServiceConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public PendingRegistration $registration;
    public OnlinePayment $payment;
    public string $passwordPlain;
    public string $receiptUrl;

    public function __construct(User $user, PendingRegistration $registration, OnlinePayment $payment, string $passwordPlain, string $receiptUrl)
    {
        $this->user = $user;
        $this->registration = $registration;
        $this->payment = $payment;
        $this->passwordPlain = $passwordPlain;
        $this->receiptUrl = $receiptUrl;
    }

    public function build()
    {
        $service = Service::find($this->registration->service_id);
        return $this->subject('Service Request Confirmed')
            ->view('mail.self-service-confirmation', [
                'user' => $this->user,
                'registration' => $this->registration,
                'payment' => $this->payment,
                'passwordPlain' => $this->passwordPlain,
                'receiptUrl' => $this->receiptUrl,
                'service' => $service,
            ]);
    }
}
