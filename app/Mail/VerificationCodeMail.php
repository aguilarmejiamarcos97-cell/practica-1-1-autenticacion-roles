<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    // Las propiedades públicas están disponibles en la vista del correo
    public function __construct(
        public string $nombre,
        public string $codigo,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu código de verificación',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-code',
        );
    }
}
