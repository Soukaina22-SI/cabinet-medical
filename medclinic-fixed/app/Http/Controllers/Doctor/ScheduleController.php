<?php
// app/Http/Controllers/Doctor/ScheduleController.php
namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $doctor    = auth()->user();
        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('doctor.schedule', compact('schedules'));
    }

    public function update(Request $request)
    {
        $doctor = auth()->user();

        // Delete all existing schedules for this doctor
        DoctorSchedule::where('doctor_id', $doctor->id)->delete();

        // Re-create from submitted form
        $days = $request->input('days', []);

        foreach ($days as $dayIndex => $day) {
            if (!empty($day['enabled'])) {
                $request->validate([
                    "days.{$dayIndex}.start_time" => 'required',
                    "days.{$dayIndex}.end_time"   => 'required',
                ]);

                DoctorSchedule::create([
                    'doctor_id'    => $doctor->id,
                    'day_of_week'  => $dayIndex,
                    'start_time'   => $day['start_time'],
                    'end_time'     => $day['end_time'],
                    'is_available' => true,
                ]);
            }
        }

        return redirect()->route('doctor.schedule')
            ->with('success', 'Vos disponibilités ont été mises à jour.');
    }
}
