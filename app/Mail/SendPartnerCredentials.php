<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPartnerCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $username;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $username, $password)
    {
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus Credenciales de Partner',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build() 
    {
        return $this->view('mail.send-partner-credentials')
            ->with([
                'name' => $this->name,
                'username' => $this->username,
                'password' => $this->password,
            ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.send-partner-credentials',
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
