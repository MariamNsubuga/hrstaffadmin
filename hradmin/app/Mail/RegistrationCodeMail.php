<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    // Define the property to hold the registration code
    public $registrationCode;

    /**
     * Create a new message instance.
     *
     * @param string $registrationCode
     */
    public function __construct($registrationCode)
    {
        // Initialize the property
        $this->registrationCode = $registrationCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Code Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_code', // Ensure this view exists
            with: [
                'registrationCode' => $this->registrationCode, // Pass the registration code to the view
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
