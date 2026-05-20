<?php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidentStatusChanged extends Mailable {
    use Queueable, SerializesModels;

    public Incident $incident;
    public string $oldStatus;
    public string $newStatus;

    function __construct(Incident $incident, string $oldStatus, string $newStatus) {
        $this->incident  = $incident;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    function envelope(): Envelope {
        return new Envelope(
            subject: 'Update on Your Incident — RentWay',
        );
    }

    function content(): Content {
        return new Content(
            view: 'emails.incident-status-changed',
        );
    }
}
