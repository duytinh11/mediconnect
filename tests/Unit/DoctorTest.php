<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DoctorTest extends TestCase
{
    use RefreshDatabase;

    private Doctor $doctor;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo test user và doctor
        $user = User::factory()->create(['role' => 'doctor']);
        $this->doctor = Doctor::factory()->create(['user_id' => $user->id]);
    }

    #[Test]
    public function can_set_daily_schedule()
    {
        $slots = ['09:00', '10:00', '11:00'];
        $date = Carbon::tomorrow()->format('Y-m-d');
        
        $result = $this->doctor->setSchedule($slots, $date);
        
        $this->assertTrue($result);
        $this->assertEquals($slots, $this->doctor->getSchedule($date));
    }

    #[Test]
    public function can_set_weekly_schedule()
    {
        $slots = ['14:00', '15:00'];
        $startDate = Carbon::tomorrow();
        
        $result = $this->doctor->setWeeklySchedule($slots, $startDate);
        
        $this->assertTrue($result);
        for ($i = 0; $i < 7; $i++) {
            $checkDate = $startDate->copy()->addDays($i);
            $this->assertEquals($slots, $this->doctor->getSchedule($checkDate->format('Y-m-d')));
        }
    }
    
    #[Test]
    public function can_check_slot_availability()
    {
        $tomorrow = Carbon::tomorrow();
        $slots = ['16:00'];
        $this->doctor->setSchedule($slots, $tomorrow->format('Y-m-d'));
        
        $this->assertTrue($this->doctor->isSlotAvailable($tomorrow->format('Y-m-d') . ' 16:00'));
        $this->assertFalse($this->doctor->isSlotAvailable($tomorrow->format('Y-m-d') . ' 17:00'));
    }
}
