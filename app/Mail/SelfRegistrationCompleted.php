<?php

namespace App\Mail;

use App\Models\OnlinePayment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SelfRegistrationCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public OnlinePayment $payment;
    public string $passwordPlain;
    public string $receiptUrl;

    public function __construct(User $user, OnlinePayment $payment, string $passwordPlain, string $receiptUrl)
    {
        $this->user = $user;
        $this->payment = $payment;
        $this->passwordPlain = $passwordPlain;
        $this->receiptUrl = $receiptUrl;
    }

    public function build()
    {
        return $this->subject('Welcome to IPAMS – Registration Confirmed')
            ->view('mail.self-registration-completed', [
                'user' => $this->user,
                'payment' => $this->payment,
                'passwordPlain' => $this->passwordPlain,
                'receiptUrl' => $this->receiptUrl,
            ]);
    }
}
