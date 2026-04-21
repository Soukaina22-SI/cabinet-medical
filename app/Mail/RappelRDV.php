<?php

// ============================================================
// app/Mail/RappelRDV.php
// ============================================================
namespace App\Mail;
 
use App\Models\RendezVous;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class RappelRDV extends Mailable
{
    use SerializesModels;
 
    public function __construct(public RendezVous $rdv) {}
 
    public function build()
    {
        return $this->subject('Rappel : votre rendez-vous de demain')
                    ->view('emails.confirmation_rdv.blade.php');
    }
}
 