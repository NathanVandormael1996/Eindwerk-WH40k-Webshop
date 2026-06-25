<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderName;
    public string $senderEmail;
    public string $messageSubject;
    public string $body;

    public function __construct(
        string $senderName,
        string $senderEmail,
        string $subject,
        string $body,
    ) {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->messageSubject = $subject;
        $this->body = $body;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Adept\'s Armoury Contact] ' . $this->messageSubject,
            replyTo: [$this->senderEmail],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
}
