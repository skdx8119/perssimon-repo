<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ContactForm extends Mailable
{
    use Queueable, SerializesModels;

    public $inputs;

    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    public function build()
    {
        return $this
            ->from(new Address(config('mail.from.address'), config('mail.from.name')))
            ->subject('お問い合わせを受け付けました')
            ->view('emails.contact')
            ->with(['inputs' => $this->inputs]);
    }
}
