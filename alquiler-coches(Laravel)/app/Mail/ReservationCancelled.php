<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationCancelled extends Mailable {
    use Queueable, SerializesModels;

    public Reservation $reservation;

    function __construct(Reservation $reservation) {
        $this->reservation = $reservation;
    }

    function envelope(): Envelope {
        return new Envelope(
            subject: 'Reservation Cancelled — RentWay',
        );
    }

    function content(): Content {
        return new Content(
            view: 'emails.reservation-cancelled',
        );
    }
}
