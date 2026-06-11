<?php

namespace Tests\Feature;

use App\Services\TelemedicineLinkService;
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
}
