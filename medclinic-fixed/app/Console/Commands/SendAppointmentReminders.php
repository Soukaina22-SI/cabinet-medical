<?php
// app/Console/Commands/SendAppointmentReminders.php
namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:send-reminders';
    protected $description = 'Send reminder emails for appointments scheduled tomorrow';

    public function handle(): void
    {
        $tomorrow = now()->addDay();

        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $tomorrow->toDateString())
            ->where('status', 'confirmed')
            ->where('reminder_sent', false)
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            if ($appointment->patient->email) {
                Mail::to($appointment->patient->email)
                    ->send(new AppointmentReminder($appointment));

                $appointment->update(['reminder_sent' => true]);
                $count++;

                $this->line("  ✅ Reminder sent to {$appointment->patient->email}");
            }
        }

        $this->info("Sent {$count} reminder(s) for {$tomorrow->format('d/m/Y')}.");
    }
}
