<?php
// ============================================================
// app/Models/Patient.php
// ============================================================
namespace App\Models;
 use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 
class Patient extends Model
{
    protected $fillable = [
        'user_id', 'date_naissance', 'sexe', 'adresse', 'cin', 'mutuelle'
    ];
 
    protected $casts = ['date_naissance' => 'date'];
 
    public function user()          { return $this->belongsTo(User::class); }
    public function rendezvous()    { return $this->hasMany(RendezVous::class); }
    public function consultations() { return $this->hasMany(Consultation::class); }
    public function dossierMedical(){ return $this->hasOne(DossierMedical::class); }
 
    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_naissance)->diffInYears(now());
    }
}
 