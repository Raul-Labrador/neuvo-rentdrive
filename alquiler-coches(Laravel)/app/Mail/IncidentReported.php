<?php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidentReported extends Mailable {
    use Queueable, SerializesModels;

    public Incident $incident;

    function __construct(Incident $incident) {
        $this->incident = $incident;
    }

    function envelope(): Envelope {
        return new Envelope(
            subject: 'New Incident Reported — RentWay',
        );
    }

    function content(): Content {
        return new Content(
            view: 'emails.incident-reported',
        );
    }
}
