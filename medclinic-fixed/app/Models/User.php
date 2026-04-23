<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone',
        'speciality', 'avatar', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ── Role Helpers ───────────────────────────────────────────
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isDoctor(): bool     { return $this->role === 'medecin'; }
    public function isSecretary(): bool  { return $this->role === 'secretaire'; }
    public function isPatient(): bool    { return $this->role === 'patient'; }

    // ── Relationships ──────────────────────────────────────────
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeDoctors($query)    { return $query->where('role', 'medecin'); }
    public function scopePatients($query)   { return $query->where('role', 'patient'); }
    public function scopeActive($query)     { return $query->where('is_active', true); }

    // ── Accessors ──────────────────────────────────────────────
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }
}
