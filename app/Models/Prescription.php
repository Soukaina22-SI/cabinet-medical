<?php
// app/Models/Prescription.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id', 'patient_id', 'doctor_id', 'pdf_path',
    ];

    public function consultation() { return $this->belongsTo(Consultation::class); }
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function doctor()       { return $this->belongsTo(User::class, 'doctor_id'); }
    public function items()        { return $this->hasMany(PrescriptionItem::class); }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }
}
