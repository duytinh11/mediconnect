<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id', 
        'specialty',
        'license_number',
        'degrees',
        'bio',
        'available_slots',
        'status',
    ];

    protected $casts = [
        'available_slots' => 'array',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city() 
    {
        return $this->belongsTo(City::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Schedule Management Methods
    public function setSchedule(array $slots, $date = null)
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        $currentSlots = $this->available_slots ?? [];
        $currentSlots[$date] = $slots;
        
        $this->available_slots = $currentSlots;
        return $this->save();
    }

    public function getSchedule($date = null)
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
        return $this->available_slots[$date] ?? [];
    }

    public function isSlotAvailable($dateTime)
    {
        $date = Carbon::parse($dateTime)->format('Y-m-d');
        $time = Carbon::parse($dateTime)->format('H:i');
        
        $slots = $this->getSchedule($date);
        return in_array($time, $slots);
    }

    // Status scope for filtering
    public function scopeActive($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('status', 'active');
        });
    }

    // Thêm phương thức quản lý lịch theo tuần
    public function setWeeklySchedule(array $slots, Carbon $startDate)
    {
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $this->setSchedule($slots, $date->format('Y-m-d'));
        }
        return true;
    }

    // Thêm phương thức quản lý lịch theo tháng
    public function setMonthlySchedule(array $slots, Carbon $startDate)
    {
        $endDate = $startDate->copy()->endOfMonth();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $this->setSchedule($slots, $currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }
        return true;
    }

    // Thêm phương thức kiểm tra số lượng lịch hẹn trong ngày
    public function getAppointmentCountForDate($date)
    {
        return $this->appointments()
            ->whereDate('scheduled_at', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    // Thêm phương thức lấy lịch trống theo khoảng thời gian
    public function getAvailableSlotsBetween($startDate, $endDate)
    {
        $slots = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $slots[$dateStr] = $this->getSchedule($dateStr);
            $current->addDay();
        }
        
        return $slots;
    }
}
