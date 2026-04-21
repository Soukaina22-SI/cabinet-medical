<?php
 
// ============================================================
// app/Models/Medecin.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Medecin extends Model
{
    protected $fillable = [
        'user_id', 'specialite', 'num_ordre', 'biographie',
        'heure_debut', 'heure_fin', 'duree_rdv'
    ];
 
    public function user()        { return $this->belongsTo(User::class); }
    public function rendezvous()  { return $this->hasMany(RendezVous::class); }
    public function consultations(){ return $this->hasMany(Consultation::class); }
 
    /**
     * Retourne les créneaux disponibles pour une date donnée
     */
    public function getDisponibilites(string $date): array
    {
        $slots = [];
        $start = \Carbon\Carbon::parse($date . ' ' . $this->heure_debut);
        $end   = \Carbon\Carbon::parse($date . ' ' . $this->heure_fin);
 
        // RDVs déjà pris ce jour
        $rdvsPris = $this->rendezvous()
            ->whereDate('date_heure', $date)
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->pluck('date_heure')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('H:i'))
            ->toArray();
 
        while ($start->copy()->addMinutes($this->duree_rdv)->lte($end)) {
            $heure = $start->format('H:i');
            $slots[] = [
                'heure'      => $heure,
                'disponible' => !in_array($heure, $rdvsPris),
            ];
            $start->addMinutes($this->duree_rdv);
        }
 
        return $slots;
    }
}