<?php

namespace App\Mail;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrganizationRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Organization $organization)
    {
    }

    public function build()
    {
        return $this->subject('Organization Registration Received')
            ->view('mail.organization-registered', ['org' => $this->organization]);
    }
}

