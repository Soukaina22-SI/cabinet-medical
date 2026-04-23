<?php
// app/Models/DoctorSchedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id', 'day_of_week',
        'start_time', 'end_time', 'is_available',
    ];

    protected $casts = ['is_available' => 'boolean'];

    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }

    public static array $days = [
        0 => 'Dimanche',
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
    ];

    public static array $dayShort = [
        0 => 'Dim', 1 => 'Lun', 2 => 'Mar',
        3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam',
    ];

    public function getDayNameAttribute(): string
    {
        return self::$days[$this->day_of_week] ?? '';
    }

    public function getDayShortAttribute(): string
    {
        return self::$dayShort[$this->day_of_week] ?? '';
    }
}
