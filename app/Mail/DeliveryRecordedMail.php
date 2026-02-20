<?php

namespace App\Mail;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryRecordedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Delivery $delivery
    ) {}

    public function envelope()
    {
        return new Envelope(
            subject: 'Delivery Recorded: ' . $this->delivery->request->request_number,
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'emails.delivery-recorded',
        );
    }

    public function attachments()
    {
        return [];
    }
}
