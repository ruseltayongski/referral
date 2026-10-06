<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentCheckApi extends Controller
{
    public function checkAppointment(Request $request)
    {
        $limit = (int) $request->input('limit', 100);

        if ($limit < 1) {
            $limit = 1;
        }

        if ($limit > 100) {
            $limit = 100;
        }

        $appointments = DB::table('activity')

            /*
            |--------------------------------------------------------------------------
            | Appointment
            |--------------------------------------------------------------------------
            */
            ->join(
                'appointment_schedule',
                'activity.appointment',
                '=',
                'appointment_schedule.id'
            )

            /*
            |--------------------------------------------------------------------------
            | Patient
            |--------------------------------------------------------------------------
            */
            ->join(
                'patients',
                'activity.patient_id',
                '=',
                'patients.id'
            )

            /*
            |--------------------------------------------------------------------------
            | Facility
            |--------------------------------------------------------------------------
            */
            ->join(
                'facility',
                'activity.referred_to',
                '=',
                'facility.id'
            )

            /*
            |--------------------------------------------------------------------------
            | Patient Account
            |--------------------------------------------------------------------------
            | Use the referring user only when level = Patient
            |--------------------------------------------------------------------------
            */
            ->leftJoin('users as patient_user', function ($join) {

                $join->on(
                    'activity.referring_md',
                    '=',
                    'patient_user.id'
                );

                $join->where(
                    'patient_user.level',
                    '=',
                    'Patient'
                );
            })

            /*
            |--------------------------------------------------------------------------
            | Preferred Doctor
            |--------------------------------------------------------------------------
            | Assuming activity.action_md contains the doctor's users.id
            |--------------------------------------------------------------------------
            */
            ->leftJoin('users as doctor', function ($join) {

                $join->on(
                    'activity.action_md',
                    '=',
                    'doctor.id'
                );
            })

            /*
            |--------------------------------------------------------------------------
            | Only accepted appointments
            |--------------------------------------------------------------------------
            */
            ->where(
                'activity.status',
                '=',
                'accepted'
            )

            /*
            |--------------------------------------------------------------------------
            | Email status
            |--------------------------------------------------------------------------
            */
            ->where(function ($query) {

                $query->where(
                    'activity.email_sent_status',
                    '=',
                    'pending'
                );

                $query->orWhere(function ($staleClaim) {

                    $staleClaim->where(
                        'activity.email_sent_status',
                        '=',
                        'processing'
                    );

                    $staleClaim->whereNotNull(
                        'activity.email_sent_claimed_until'
                    );

                    $staleClaim->where(
                        'activity.email_sent_claimed_until',
                        '<=',
                        Carbon::now()
                    );
                });
            })

            /*
            |--------------------------------------------------------------------------
            | Select the data we need
            |--------------------------------------------------------------------------
            */
            ->select([

                // Unique activity ID
                'activity.id as activity_id',

                // Appointment ID
                'appointment_schedule.id as appointment_id',

                // Patient
                DB::raw("
                    CONCAT_WS(
                        ' ',
                        patients.fname,
                        patients.mname,
                        patients.lname
                    ) AS patient_name
                "),

                // Patient email
                'patient_user.email as patient_email',

                // Requested start
                DB::raw("
                    TIMESTAMP(
                        appointment_schedule.appointed_date,
                        appointment_schedule.appointed_time
                    ) AS requested_at
                "),

                // Requested end
                DB::raw("
                    TIMESTAMP(
                        appointment_schedule.appointed_date,
                        appointment_schedule.appointedTime_to
                    ) AS requested_end_at
                "),

                // Preferred doctor
                DB::raw("
                    CONCAT_WS(
                        ' ',
                        doctor.fname,
                        doctor.mname,
                        doctor.lname
                    ) AS preferred_doctor
                "),

                // Facility
                'facility.name as facility',

                'facility.address as facility_address',

                // Video URL
                'activity.video_call_link as video_url',

                // Activity status
                'activity.status as appointment_status',

                // Email status
                'activity.email_sent_status as email_sent_status',

                // Claim
                'activity.email_sent_claimed_until as claimed_until'
            ])

            /*
            |--------------------------------------------------------------------------
            | Multiple records
            |--------------------------------------------------------------------------
            |
            | activity.id gives us a unique secondary ordering.
            |
            */
            ->orderBy(
                'appointment_schedule.appointed_date',
                'asc'
            )

            ->orderBy(
                'appointment_schedule.appointed_time',
                'asc'
            )

            ->orderBy(
                'activity.id',
                'asc'
            )

            ->take($limit)

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Format response
        |--------------------------------------------------------------------------
        */

        $data = $appointments->map(function ($appointment) {

            return array(
                'appointment_id' => (string) $appointment->appointment_id,
                'activity_id' => (string) $appointment->activity_id,
                'patient_name' => $appointment->patient_name,

                'patient_email' => $appointment->patient_email,

                'requested_at' => $appointment->requested_at
                    ? Carbon::parse($appointment->requested_at)
                        ->toIso8601String()
                    : null,

                'requested_end_at' => $appointment->requested_end_at
                    ? Carbon::parse($appointment->requested_end_at)
                        ->toIso8601String()
                    : null,

                'preferred_doctor' => $appointment->preferred_doctor,

                'facility' => $appointment->facility,

                'facility_address' => $appointment->facility_address,

                'video_url' => $appointment->video_url,

                'appointment_status' => $appointment->appointment_status,

                'email_sent_status' => $appointment->email_sent_status,

                'claimed_until' => $appointment->claimed_until
                    ? Carbon::parse($appointment->claimed_until)
                        ->toIso8601String()
                    : null,
            );
        });

        return response()->json(array(
            'data' => $data->values(),
        ));
    }

   public function updateEmailStatus(Request $request, string $activity_id)
{
    $validated = $request->validate([
        'email_sent_status' => 'required|in:processing,sent,expired',
        'claim_token' => 'required_if:email_sent_status,processing,sent|nullable|string|max:255',
        'claimed_until' => 'required_if:email_sent_status,processing|nullable|date',
    ]);

    $status = $validated['email_sent_status'];
    $activityId = $activity_id;

    if ($status === 'processing') {
        $now = Carbon::now(config('app.timezone'));
        $claimedUntil = Carbon::parse($validated['claimed_until'])
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');

        $claimed = DB::table('activity')
            ->where('id', $activityId)
            ->where(function ($query) use ($now) {
                $query->where('email_sent_status', 'pending')
                    ->orWhere(function ($query) use ($now) {
                        $query->where('email_sent_status', 'processing')
                            ->where(function ($query) use ($now) {
                                $query->whereNull('email_sent_claimed_until')
                                    ->orWhere(
                                        'email_sent_claimed_until',
                                        '<=',
                                        $now->format('Y-m-d H:i:s')
                                    );
                            });
                    });
            })
            ->update([
                'email_sent_status' => 'processing',
                'email_sent_claim_token' => $validated['claim_token'],
                'email_sent_claimed_until' => $claimedUntil,
                'email_sent_at' => null,
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]);

        if ($claimed === 0) {
            if (! DB::table('activity')->where('id', $activityId)->exists()) {
                return response()->json(['message' => 'Activity not found.'], 404);
            }

            return response()->json([
                'activity_id' => $activityId,
                'claimed' => false,
            ]);
        }

        return response()->json([
            'activity_id' => $activityId,
            'email_sent_status' => 'processing',
            'claimed' => true,
            'claimed_until' => Carbon::parse($claimedUntil)->toIso8601String(),
        ]);
    }

    if ($status === 'sent') {
        $now = Carbon::now(config('app.timezone'));
        $claimToken = $validated['claim_token'];

        $updated = DB::table('activity')
            ->where('id', $activityId)
            ->where(function ($query) use ($claimToken) {
                $query->where(function ($query) use ($claimToken) {
                    $query->where('email_sent_status', 'processing')
                        ->where('email_sent_claim_token', $claimToken);
                })->orWhere(function ($query) use ($claimToken) {
                    // Allow a retry if the first "sent" update succeeded but its response was lost.
                    $query->where('email_sent_status', 'sent')
                        ->where('email_sent_claim_token', $claimToken);
                });
            })
            ->update([
                'email_sent_status' => 'sent',
                'email_sent_claimed_until' => null,
                'email_sent_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]);

        if ($updated === 0) {
            if (! DB::table('activity')->where('id', $activityId)->exists()) {
                return response()->json(['message' => 'Activity not found.'], 404);
            }

            return response()->json([
                'message' => 'Claim token is stale or does not match this activity.',
            ], 409);
        }

        return response()->json([
            'activity_id' => $activityId,
            'email_sent_status' => 'sent',
            'claimed_until' => null,
        ]);
    }

    $now = Carbon::now(config('app.timezone'));
    $updated = DB::table('activity')
        ->where('id', $activityId)
        ->where('email_sent_status', '!=', 'sent')
        ->update([
            'email_sent_status' => 'expired',
            'email_sent_claim_token' => null,
            'email_sent_claimed_until' => null,
            'email_sent_at' => null,
            'updated_at' => $now->format('Y-m-d H:i:s'),
        ]);

    if ($updated === 0) {
        $activity = DB::table('activity')
            ->where('id', $activityId)
            ->first(['id', 'email_sent_status']);

        if (! $activity) {
            return response()->json(['message' => 'Activity not found.'], 404);
        }

        if ($activity->email_sent_status !== 'expired') {
            return response()->json([
                'message' => 'A sent activity cannot be marked expired.',
            ], 409);
        }
    }

    return response()->json([
        'activity_id' => $activityId,
        'email_sent_status' => 'expired',
        'claimed_until' => null,
    ]);
    }
}
