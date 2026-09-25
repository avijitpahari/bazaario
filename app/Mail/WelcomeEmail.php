<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $role;
    public string $ctaUrl;
    public string $supportUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;

        if ($user->role === 'seller') {
            $this->ctaUrl = route('seller.pending');
        } else {
            $this->ctaUrl = route('products.index');
        }

        $this->supportUrl = url('/');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Bazaario Marketplace',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'name'       => $this->name,
                'email'      => $this->email,
                'role'       => $this->role,
                'ctaUrl'     => $this->ctaUrl,
                'supportUrl' => $this->supportUrl,
            ],
        );
    }
}
