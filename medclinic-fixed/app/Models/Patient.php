<?php
// app/Models/Patient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'cin', 'phone', 'email',
        'date_of_birth', 'gender', 'address', 'blood_type', 'allergies', 'medical_notes',
    ];

    protected $casts = ['date_of_birth' => 'date'];

    public function user()          { return $this->belongsTo(User::class); }
    public function appointments()  { return $this->hasMany(Appointment::class); }
    public function consultations() { return $this->hasMany(Consultation::class); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name',  'like', "%{$term}%")
              ->orWhere('cin',        'like', "%{$term}%")
              ->orWhere('phone',      'like', "%{$term}%");
        });
    }
}
