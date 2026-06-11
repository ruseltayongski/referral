<?php

namespace App\Services;

use App\AppointmentSchedule;
use App\Tracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class TelemedicineLinkService
{
    public static function buildSignedUrl($tracking, array $additional = [])
    {
        $parameters = array_merge([
            'id' => $tracking->id,
            'from_fact' => 0,
            'code' => $tracking->code,
            'form_type' => 'normal',
            'telemed' => 1,
            'referring_md' => 'yes',
            'display_name' => $tracking->patient_name ?? null,
            'role' => 'patient',
        ], $additional);

        $parameters = array_filter($parameters, function ($value) {
            return $value !== null && $value !== '';
        });

        return URL::temporarySignedRoute(
            'doctor.telemedicine',
            self::resolveExpiration($tracking),
            $parameters
        );
    }

    public static function buildSignedUrlForTrackingId($trackingId, array $additional = [])
    {
        $tracking = Tracking::find($trackingId);

        if (!$tracking) {
            return null;
        }

        return self::buildSignedUrl($tracking, $additional);
    }

    public static function resolveExpiration($tracking)
    {
        $expiration = now()->addHours(2);

        if (!empty($tracking->appointmentId)) {
            $appointmentSchedule = AppointmentSchedule::find($tracking->appointmentId);

            if ($appointmentSchedule && $appointmentSchedule->appointed_date) {
                $startTime = $appointmentSchedule->appointed_time ?: '00:00:00';
                $endTime = $appointmentSchedule->appointedTime_to;

                if ($endTime) {
                    $expiration = Carbon::parse($appointmentSchedule->appointed_date . ' ' . $endTime);
                } else {
                    $startDateTime = Carbon::parse($appointmentSchedule->appointed_date . ' ' . $startTime);
                    $expiration = $startDateTime->copy()->addHours(2);
                }
            }
        }

        if ($expiration->isPast()) {
            $expiration = now()->addHours(2);
        }

        return $expiration;
    }
}
