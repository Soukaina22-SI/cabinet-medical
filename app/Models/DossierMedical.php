<?php
// ============================================================
// app/Models/DossierMedical.php
// ============================================================
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class DossierMedical extends Model
{
    protected $table = 'dossiers_medicaux';
 
    protected $fillable = [
        'patient_id', 'antecedents', 'allergies', 'groupe_sanguin'
    ];
 
    public function patient() { return $this->belongsTo(Patient::class); }
 
    public function getHistorique()
    {
        return $this->patient->consultations()
            ->with(['medecin.user', 'ordonnance'])
            ->orderByDesc('date_heure')
            ->get();
    }
}