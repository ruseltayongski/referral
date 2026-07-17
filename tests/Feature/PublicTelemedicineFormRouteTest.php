<?php

namespace Tests\Feature;

use App\Feedback;
use App\Http\Controllers\doctor\ReferralCtrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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

    public function testGuestCanPostFeedbackMessageWithoutAuthentication()
    {
        Feedback::where('code', 'guest-chat-test')->delete();
        Session::flush();

        $controller = new ReferralCtrl();
        $request = Request::create('/doctor/feedback', 'POST', [
            'code' => 'guest-chat-test',
            'message' => 'Guest message from public chat',
            'display_name' => 'Guest User',
        ]);

        $response = $controller->saveFeedback($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertDatabaseHas('feedback', [
            'code' => 'guest-chat-test',
            'sender' => 0,
            'message' => 'Guest message from public chat',
        ]);
    }
}
