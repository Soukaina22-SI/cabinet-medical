<?php
 
// ============================================================
// app/Models/User.php
// ============================================================
namespace App\Models;
 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 
class User extends Authenticatable
{
    use Notifiable;
 
    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'role', 'telephone', 'actif'
    ];
 
    protected $hidden = ['password', 'remember_token'];
 
    protected $casts = [
    'actif' => 'boolean',
];
 
    // Relations
    public function medecin() { return $this->hasOne(Medecin::class); }
    public function patient() { return $this->hasOne(Patient::class); }
 
    // Helpers
    public function isAdmin()     { return $this->role === 'admin'; }
    public function isMedecin()   { return $this->role === 'medecin'; }
    public function isSecretaire(){ return $this->role === 'secretaire'; }
    public function isPatient()   { return $this->role === 'patient'; }
 
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
 