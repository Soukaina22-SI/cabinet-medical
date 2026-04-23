<?php
// app/Models/Consultation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id', 'patient_id', 'doctor_id',
        'symptoms', 'diagnosis', 'notes',
        'weight', 'height', 'blood_pressure', 'temperature',
    ];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient()     { return $this->belongsTo(Patient::class); }
    public function doctor()      { return $this->belongsTo(User::class, 'doctor_id'); }
    public function prescription(){ return $this->hasOne(Prescription::class); }
}


// ----------------------------------------------------------------
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
}


// ----------------------------------------------------------------
// app/Models/PrescriptionItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id', 'medication_name', 'dosage',
        'frequency', 'duration', 'instructions',
    ];

    public function prescription() { return $this->belongsTo(Prescription::class); }
}


// ----------------------------------------------------------------
// app/Models/DoctorSchedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time', 'end_time', 'is_available',
    ];

    protected $casts = ['is_available' => 'boolean'];

    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }

    public static array $days = [
        0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi',
        3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi',
    ];

    public function getDayNameAttribute(): string
    {
        return self::$days[$this->day_of_week] ?? '';
    }
}
