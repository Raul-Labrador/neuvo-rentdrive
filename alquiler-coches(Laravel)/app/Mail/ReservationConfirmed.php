<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends Mailable {
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public string $carName;
    public int $days;

    function __construct(Reservation $reservation, string $carName, int $days) {
        $this->reservation = $reservation;
        $this->carName     = $carName;
        $this->days        = $days;
    }

    function envelope(): Envelope {
        return new Envelope(
            subject: 'Reservation Confirmed — RentWay',
        );
    }

    function content(): Content {
        return new Content(
            view: 'emails.reservation-confirmed',
        );
    }
}
