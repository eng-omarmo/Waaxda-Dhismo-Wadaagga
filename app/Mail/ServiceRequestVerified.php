<?php

namespace App\Mail;

use App\Models\PaymentVerification;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceRequestVerified extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequest $request;
    public PaymentVerification $payment;
    public string $receiptUrl;

    public function __construct(ServiceRequest $request, PaymentVerification $payment)
    {
        $this->request = $request;
        $this->payment = $payment;
        $this->receiptUrl = URL::temporarySignedRoute('receipt.show', now()->addDays(7), ['payment' => $payment->id]);
    }

    public function build()
    {
        return $this->subject('Your service request has been verified')
            ->view('mail.service-request-verified', [
                'request' => $this->request,
                'payment' => $this->payment,
                'receiptUrl' => $this->receiptUrl,
            ]);
    }
}
