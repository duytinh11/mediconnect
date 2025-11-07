<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment, public string $type) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $lines = match ($this->type) {
            'booking' => ['A new appointment has been booked.', 'Status: Pending confirmation.'],
            'reschedule' => ['An appointment has been rescheduled.', 'Please review the new time.'],
            'cancel' => ['An appointment has been canceled.'],
            default => ['Appointment status updated.'],
        };

        $message = (new MailMessage)
            ->subject('Appointment Update')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line("Doctor: {$this->appointment->doctor->user->name}")
            ->line("Patient: {$this->appointment->patient->user->name}")
            ->line('Scheduled at: ' . $this->appointment->scheduled_at->format('Y-m-d H:i'));

        foreach ($lines as $line) {
            $message->line($line);
        }

        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'type' => $this->type,
            'scheduled_at' => $this->appointment->scheduled_at,
            'doctor' => $this->appointment->doctor->user->name,
            'patient' => $this->appointment->patient->user->name,
        ];
    }
}
