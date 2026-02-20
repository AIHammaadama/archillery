<?php

namespace App\Mail;

use App\Models\ProcurementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProcurementRequest $request,
        public string $reason
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: 'Request Rejected: ' . $this->request->request_number,
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'emails.request-rejected',
        );
    }

    public function attachments()
    {
        return [];
    }
}
