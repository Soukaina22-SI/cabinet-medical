<?php
// ============================================================
// app/Mail/ConfirmationRDV.php
// ============================================================
namespace App\Mail;
 
use App\Models\RendezVous;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class ConfirmationRDV extends Mailable
{
    use SerializesModels;
 
    public function __construct(public RendezVous $rdv) {}
 
    public function build()
    {
        return $this->subject('Confirmation de votre rendez-vous')
                    ->view('emails.confirmation_rdv')
                    ->with([
                        'rdv'     => $this->rdv,
                        'patient' => $this->rdv->patient->user,
                        'medecin' => $this->rdv->medecin->user,
                    ]);
    }
}