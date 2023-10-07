<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendingEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Header
        return new Envelope(
            // from: new Address('aaa@gmail.com', 'Asep Saepudin'),
            // replyTo: [
            //     new Address('bbb@gmail.com', 'Warung Asep')
            // ],
            subject: 'Request new password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Isi Email
        return new Content(
            view: 'emails.sending-email',
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
