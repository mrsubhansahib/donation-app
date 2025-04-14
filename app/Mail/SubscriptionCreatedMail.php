<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $subscription;

    public function __construct($user, $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }

    public function build()
    {
        return $this->subject('🎉 Subscription Activated')
            ->view('pages.emails.subscription_mail');
    }


    public function envelope()
    {
        return new Envelope(
            subject: 'Subscription Created Mail',
        );
    }


    public function content()
    {
        return new Content(
            view: 'view.name',
        );
    }


    public function attachments()
    {
        return [];
    }
}
