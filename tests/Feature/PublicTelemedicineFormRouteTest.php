<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicTelemedicineFormRouteTest extends TestCase
{
    public function testPublicTelemedicineFormRouteReturnsNotFoundForUnknownTracking()
    {
        $response = $this->get('/video/public/form-data/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Tracking record not found',
            ]);
    }

    public function testTelemedicineRouteDoesNotRedirectToLoginForGuestAccess()
    {
        $response = $this->get('/doctor/telemedicine?form_type=normal&telemed=1');

        $response->assertStatus(200);
    }
}
