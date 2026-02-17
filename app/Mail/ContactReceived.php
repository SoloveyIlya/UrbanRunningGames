<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новое обращение с сайта: ' . $this->message->topic_label,
            replyTo: $this->message->email ? [$this->message->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-received'
        );
    }
}
