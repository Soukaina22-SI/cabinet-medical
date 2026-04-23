<?php
// app/Models/Appointment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_date',
        'status', 'reason', 'notes', 'reminder_sent',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'reminder_sent'    => 'boolean',
    ];

    public function patient()      { return $this->belongsTo(Patient::class); }
    public function doctor()       { return $this->belongsTo(User::class, 'doctor_id'); }
    public function consultation() { return $this->hasOne(Consultation::class); }

    // ── Status helpers ─────────────────────────────────────────
    public function isPending():   bool { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'   => '<span class="badge bg-warning">En attente</span>',
            'confirmed' => '<span class="badge bg-success">Confirmé</span>',
            'cancelled' => '<span class="badge bg-danger">Annulé</span>',
            'completed' => '<span class="badge bg-secondary">Terminé</span>',
            default     => '<span class="badge bg-light text-dark">' . $this->status . '</span>',
        };
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now())
                     ->where('status', '!=', 'cancelled');
    }
}
