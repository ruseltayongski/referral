<?php

namespace App\Services;

use App\AppointmentSchedule;
use App\Tracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

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

    /**
     * Build the guest-messenger signed URLs (fetch + send) for a tracking/reco code.
     * Shares the same expiration window as the video link so the chat doesn't
     * outlive (or die before) the call itself.
     */
    public static function buildMessengerUrls($tracking, $senderId = 0, array $additional = [])
    {
        $expiration = self::resolveExpiration($tracking);

        $fetchParameters = array_merge([
            'code' => $tracking->code,
        ], $additional);

        $sendParameters = array_merge([
            'code' => $tracking->code,
            'sender_id' => $senderId,
        ], $additional);

        $fetchParameters = array_filter($fetchParameters, function ($value) {
            return $value !== null && $value !== '';
        });

        $sendParameters = array_filter($sendParameters, function ($value) {
            return $value !== null && $value !== '';
        });

        return [
            'fetch' => URL::temporarySignedRoute(
                'api.reco.messages',
                $expiration,
                $fetchParameters
            ),
            'send' => URL::temporarySignedRoute(
                'api.reco.message.send',
                $expiration,
                $sendParameters
            ),
        ];
    }
    public static function buildMessengerUrlsForTrackingId($trackingId, $senderId = 0, array $additional = [])
    {
        $tracking = Tracking::find($trackingId);

        if (!$tracking) {
            return null;
        }

        return self::buildMessengerUrls($tracking, $senderId, $additional);
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

        // Do NOT extend expiration for past appointments - allow token to expire naturally
        // Removing the automatic renewal that was allowing expired tokens to work

        return $expiration;
    }
}