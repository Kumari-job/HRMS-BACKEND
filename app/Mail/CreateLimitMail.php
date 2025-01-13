<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreateLimitMail extends Mailable
{
    use Queueable, SerializesModels;


    public $type;
    public $remaining;
    /**
     * Create a new message instance.
     */
    public function __construct($type, $remaining)
    {
        $this->type = $type;
        $this->remaining = $remaining;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ucfirst($this->type) . " limit",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.limit',
            with: [
                'remaining' => $this->remaining,
                'type' => $this->type,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
