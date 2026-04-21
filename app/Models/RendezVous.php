<?php
// ============================================================
// app/Models/RendezVous.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class RendezVous extends Model
{
    protected $table = 'rendezvous';
 
    protected $fillable = [
        'patient_id', 'medecin_id', 'date_heure', 'motif', 'statut', 'notes'
    ];
 
    protected $casts = ['date_heure' => 'datetime'];
 
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function medecin()      { return $this->belongsTo(Medecin::class); }
    public function consultation() { return $this->hasOne(Consultation::class); }
 
    public function confirmer(): bool
    {
        return $this->update(['statut' => 'confirme']);
    }
 
    public function annuler(): bool
    {
        return $this->update(['statut' => 'annule']);
    }
 
    public function terminer(): bool
    {
        return $this->update(['statut' => 'termine']);
    }
}