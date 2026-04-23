<?php
// app/Mail/AppointmentConfirmed.php
namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Confirmation de votre rendez-vous — MedClinic',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-confirmed',
        );
    }
}
