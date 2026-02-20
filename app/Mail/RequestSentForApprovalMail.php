<?php

namespace App\Mail;

use App\Models\ProcurementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestSentForApprovalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProcurementRequest $request
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: 'Request Awaiting Approval: ' . $this->request->request_number,
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'emails.request-sent-for-approval',
        );
    }

    public function attachments()
    {
        return [];
    }
}
