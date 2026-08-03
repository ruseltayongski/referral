<?php

namespace Tests\Feature;

use App\AppointmentSchedule;
use App\Services\TelemedicineLinkService;
use Carbon\Carbon;
use Tests\TestCase;

class TelemedicineSignedUrlTest extends TestCase
{
    public function test_signed_link_generation_uses_the_telemedicine_route_and_signature()
    {
        $tracking = new \stdClass();
        $tracking->id = 42;
        $tracking->code = 'TEST-001';
        $tracking->patient_name = 'Juan Dela Cruz';
        $tracking->appointmentId = null;

        $url = TelemedicineLinkService::buildSignedUrl($tracking, [
            'form_type' => 'normal',
            'telemed' => 1,
            'referring_md' => 'yes',
        ]);

        $this->assertTrue(strpos($url, '/doctor/telemedicine') !== false);
        $this->assertTrue(strpos($url, 'signature=') !== false);
        $this->assertTrue(strpos($url, 'id=42') !== false);
    }

    public function test_resolve_expiration_uses_the_latest_appointment_schedule_override()
    {
        $tracking = new \stdClass();
        $tracking->id = 43;
        $tracking->code = 'TEST-002';
        $tracking->appointmentId = 999;

        $stub = new class extends TelemedicineLinkService {
            protected static function resolveAppointmentSchedule($appointmentScheduleId)
            {
                $appointmentSchedule = new AppointmentSchedule();
                $appointmentSchedule->id = 123;
                $appointmentSchedule->appointed_date = '2026-08-03';
                $appointmentSchedule->appointed_time = '10:00:00';
                $appointmentSchedule->appointedTime_to = '11:00:00';

                return $appointmentSchedule;
            }
        };

        $expiration = $stub::resolveExpiration($tracking, 123);

        $this->assertEquals(Carbon::parse('2026-08-03 11:00:00'), $expiration);
    }
}
