<?php

// ============================================================
// app/Models/Consultation.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Consultation extends Model
{
    protected $fillable = [
        'rendezvous_id', 'medecin_id', 'patient_id',
        'date_heure', 'diagnostic', 'compte_rendu', 'prix'
    ];
 
    protected $casts = ['date_heure' => 'datetime'];
 
    public function rendezvous() { return $this->belongsTo(RendezVous::class); }
    public function medecin()    { return $this->belongsTo(Medecin::class); }
    public function patient()    { return $this->belongsTo(Patient::class); }
    public function ordonnance() { return $this->hasOne(Ordonnance::class); }
}
 