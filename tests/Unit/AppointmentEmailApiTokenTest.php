<?php

namespace Tests\Unit;

use App\Http\Middleware\VerifyAppointmentEmailApiToken;
use Illuminate\Http\Request;
use Tests\TestCase;

class AppointmentEmailApiTokenTest extends TestCase
{
    public function test_missing_bearer_token_is_rejected()
    {
        config(array(
            'services.appointment_email_api_token' => 'test-secret-token',
        ));

        $response = $this->middleware()->handle(
            Request::create('/api/internal/appointment-email-candidates', 'GET'),
            function () {
                return response()->json(array('passed' => true));
            }
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_invalid_bearer_token_is_rejected()
    {
        config(array(
            'services.appointment_email_api_token' => 'test-secret-token',
        ));

        $request = Request::create(
            '/api/internal/appointments/123/email-status',
            'PATCH',
            array(),
            array(),
            array(),
            array('HTTP_AUTHORIZATION' => 'Bearer wrong-token')
        );

        $response = $this->middleware()->handle($request, function () {
            return response()->json(array('passed' => true));
        });

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_valid_bearer_token_is_allowed()
    {
        config(array(
            'services.appointment_email_api_token' => 'test-secret-token',
        ));

        $request = Request::create(
            '/api/internal/appointment-email-candidates',
            'GET',
            array(),
            array(),
            array(),
            array('HTTP_AUTHORIZATION' => 'Bearer test-secret-token')
        );

        $response = $this->middleware()->handle($request, function () {
            return response()->json(array('passed' => true));
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_missing_server_token_fails_closed()
    {
        config(array(
            'services.appointment_email_api_token' => null,
        ));

        $response = $this->middleware()->handle(
            Request::create('/api/internal/appointment-email-candidates', 'GET'),
            function () {
                return response()->json(array('passed' => true));
            }
        );

        $this->assertEquals(503, $response->getStatusCode());
    }

    private function middleware()
    {
        return new VerifyAppointmentEmailApiToken();
    }
}