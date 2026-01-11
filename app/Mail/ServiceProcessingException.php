<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceProcessingException extends Mailable
{
    use Queueable, SerializesModels;

    public ServiceRequest $request;
    public string $messageText;

    public function __construct(ServiceRequest $request, string $messageText)
    {
        $this->request = $request;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject('Issue with your service request')
            ->view('mail.service-processing-exception', [
                'request' => $this->request,
                'messageText' => $this->messageText,
            ]);
    }
}
