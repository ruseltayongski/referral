<?php

namespace App\Services;

use App\WebsocketNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebsocketNotificationService
{
    public function record($event)
    {
        $notification = $this->normalize($event);
        if (!$notification || !$notification['code']) {
            return;
        }

        $recipients = $notification['facilities']
            ? $this->recipients(
                $notification['facilities'],
                $notification['exclude_user_id'],
                $notification['subopd_id']
            )
            : [];

        if (in_array($notification['type'], [
            'new_referral', 'transferred', 'accepted', 'declined', 'rejected',
            'cancelled', 'cancel_undone', 'arrived', 'departed', 'not_arrived',
            'admitted', 'discharged', 'reco_message',
        ], true)) {
            $recipients = array_merge(
                $recipients,
                $this->patientRecipients($notification['code'], $notification['exclude_user_id'])
            );
        }

        $recipientsById = [];
        foreach ($recipients as $recipient) {
            $recipientsById[(int) $recipient->id] = $recipient;
        }

        foreach ($recipientsById as $userId => $recipient) {
            WebsocketNotification::updateOrCreate(
                ['user_id' => $userId, 'notification_key' => $notification['key']],
                [
                    'recipient_level' => $recipient->level,
                    'event_type' => $notification['type'],
                    'title' => $notification['title'],
                    'body' => $notification['body'],
                    'related_code' => $notification['code'],
                    'related_id' => $notification['related_id'],
                    'destination_url' => $this->destinationUrl($notification, $recipient),
                    'occurred_at' => $notification['occurred_at'],
                ]
            );
        }
    }

    private function normalize($event)
    {
        $payload = isset($event->payload) && is_array($event->payload) ? $event->payload : [];
        $eventClass = class_basename($event);
        $code = $payload['patient_code'] ?? $payload['code'] ?? null;
        $patient = trim(strip_tags($payload['patient_name'] ?? ''));
        $facilities = [];
        $type = '';
        $title = '';
        $body = '';
        $relatedId = $payload['feedback_id'] ?? $payload['reco_id'] ?? null;
        $excludeUserId = null;
        $subopdId = null;
        $referredFrom = null;
        $referredTo = null;
        $telemedicine = false;

        if ($eventClass === 'NewReferral') {
            $status = $payload['status'] ?? 'referred';
            $type = $status === 'transferred' ? 'transferred' : 'new_referral';
            $title = $status === 'transferred' ? 'Transferred referral' : 'New referral';
            $referringDoctor = trim(strip_tags($payload['referring_md'] ?? ''));
            if ($referringDoctor === '') {
                $referringDoctor = $this->referringDoctorName($code);
            }
            $body = $patient . ' was referred';
            if ($referringDoctor !== '') {
                $body .= ' by Dr. ' . $referringDoctor;
            }
            if (!empty($payload['referring_name'])) {
                $body .= ' of ' . $payload['referring_name'];
            }
            $facilities = [$payload['referred_to'] ?? null];
            $referredFrom = $payload['referred_from'] ?? null;
            $referredTo = $payload['referred_to'] ?? null;
            $telemedicine = !empty($payload['telemedicine']);
            if (!empty($payload['telemedicine'])) {
                $subopdId = $payload['subOpdId'] ?? $payload['subopd_id'] ?? null;
            }
            if ($status === 'transferred') {
                $facilities[] = $payload['referred_from'] ?? null;
                $facilities[] = $payload['referred_from_track'] ?? null;
                $facilities[] = $payload['count_referred_from'] ?? null;
            }
        } elseif ($eventClass === 'SocketReco') {
            $type = 'reco_message';
            $title = 'New ReCo message';
            $message = html_entity_decode(strip_tags($payload['message'] ?? ''), ENT_QUOTES, 'UTF-8');
            $body = trim(($payload['name_sender'] ?? 'A user') . ': ' . $message);
            $excludeUserId = $payload['userid_sender'] ?? null;
            $facilities = $this->recoFacilities($code, $payload['sender_facility'] ?? null);
            if (!$relatedId && $code) {
                $relatedId = DB::table('feedback')->where('code', $code)->orderBy('id', 'desc')->value('id');
            }
        } elseif ($eventClass === 'SocketReferralAccepted') {
            $type = 'accepted';
            $title = 'Referral accepted';
            $body = $patient . ' was accepted by Dr. ' . ($payload['accepting_doctor'] ?? '') . ' of ' . ($payload['accepting_facility_name'] ?? '');
            $facilities = [$payload['referred_from'] ?? null];
            $referredFrom = $payload['referred_from'] ?? null;
            $referredTo = $payload['referred_to'] ?? null;
            $telemedicine = !empty($payload['telemedicine']);
            if (!empty($payload['telemedicine'])) {
                $facilities[] = $payload['referred_to'] ?? null;
            }
            $excludeUserId = $payload['accepting_doctor_id'] ?? null;
        } elseif ($eventClass === 'SocketReferralRejected') {
            $declined = ($payload['status'] ?? '') === 'declined';
            $type = $declined ? 'declined' : 'rejected';
            $title = $declined ? 'Referral declined' : 'Referral redirected';
            $body = $patient . ($declined ? ' was declined by ' : ' was recommended for redirection by ')
                . 'Dr. ' . ($payload['rejected_by'] ?? '') . ' of ' . ($payload['rejected_by_facility'] ?? '');
            $facilities = [$payload['referred_from'] ?? null];
            $referredFrom = $payload['referred_from'] ?? null;
            $referredTo = $payload['referred_to'] ?? null;
            $telemedicine = !empty($payload['telemed']);
            if (!empty($payload['telemed'])) {
                $facilities[] = $payload['referred_to'] ?? null;
            }
        } elseif ($eventClass === 'SocketReferralUpdate') {
            $notifType = $payload['notif_type'] ?? '';
            if (!in_array($notifType, ['cancel referral', 'undo cancel'], true)) {
                return null;
            }
            $cancelled = $notifType === 'cancel referral';
            $type = $cancelled ? 'cancelled' : 'cancel_undone';
            $title = $cancelled ? 'Referral cancelled' : 'Cancellation undone';
            $body = $patient . ($cancelled ? "'s referral was cancelled" : ' was referred again');
            $facilities = [$payload['referred_to'] ?? null];
            $referredFrom = $payload['referred_from'] ?? null;
            $referredTo = $payload['referred_to'] ?? null;
            if (!empty($payload['admin']) && $payload['admin'] === 'yes') {
                $facilities[] = $payload['referred_from'] ?? null;
            }
        } elseif (in_array($eventClass, [
            'SocketReferralArrived', 'SocketReferralDeparted', 'SocketReferralNotArrived',
            'SocketReferralAdmitted', 'SocketReferralDischarged',
        ], true)) {
            if ($eventClass === 'SocketReferralDischarged' && ($payload['status'] ?? '') !== 'discharged') {
                return null;
            }
            $definitions = [
                'SocketReferralArrived' => ['arrived', 'Referral arrived', ' arrived at '],
                'SocketReferralDeparted' => ['departed', 'Referral departed', ' departed from '],
                'SocketReferralNotArrived' => ['not_arrived', 'Referral not arrived', ' was not recorded as arrived at '],
                'SocketReferralAdmitted' => ['admitted', 'Referral admitted', ' was admitted at '],
                'SocketReferralDischarged' => ['discharged', 'Referral discharged', ' was discharged from '],
            ];
            [$type, $title, $verb] = $definitions[$eventClass];
            $facilityName = $payload['current_facility'] ?? $payload['departed_by_facility'] ?? '';
            $body = $patient . $verb . $facilityName;
            $facilities = [$payload['referred_from'] ?? null];
            $referredFrom = $payload['referred_from'] ?? null;
            $referredTo = $payload['referred_to'] ?? null;
            $telemedicine = !empty($payload['telemed']);
            if (!empty($payload['telemed'])) {
                $facilities[] = $payload['referred_to'] ?? null;
            }
        } else {
            return null;
        }

        $facilities = array_values(array_unique(array_filter(array_map('intval', $facilities))));
        if (!$code) {
            return null;
        }

        $type = Str::limit($type, 50, '');
        $body = Str::limit(trim($body), 500, '');
        $eventDate = '';
        foreach (['date_rejected', 'date_accepted', 'referred_date', 'arrived_date', 'departed_date', 'cancelled_date', 'undo_date', 'date_now'] as $dateKey) {
            if (!empty($payload[$dateKey])) {
                $eventDate = $payload[$dateKey];
                break;
            }
        }
        $keyParts = [
            get_class($event), $type, $code,
            $payload['activity_id'] ?? $payload['tracking_id'] ?? $relatedId ?? '',
            $eventDate,
            $payload['message'] ?? '',
        ];
        $key = hash('sha256', json_encode($keyParts));
        $occurredAt = $eventDate ?: null;
        $occurredAt = $occurredAt && strtotime($occurredAt) ? date('Y-m-d H:i:s', strtotime($occurredAt)) : null;

        return [
            'type' => $type,
            'title' => Str::limit($title, 100, ''),
            'body' => $body,
            'code' => (string) $code,
            'related_id' => $relatedId ? (int) $relatedId : null,
            'url' => $type === 'reco_message'
                ? url('reco')
                : url('doctor/referred?referredCode=' . rawurlencode($code)),
            'referred_from' => $referredFrom ? (int) $referredFrom : null,
            'referred_to' => $referredTo ? (int) $referredTo : null,
            'filter_ref' => $telemedicine ? 1 : 0,
            'facilities' => $facilities,
            'subopd_id' => $subopdId ? (int) $subopdId : null,
            'exclude_user_id' => $excludeUserId ? (int) $excludeUserId : null,
            'key' => $key,
            'occurred_at' => $occurredAt,
        ];
    }

    public function referringDoctorName($code)
    {
        if (!$code) {
            return '';
        }

        $doctorId = DB::table('activity')
            ->where('code', $code)
            ->whereIn('status', ['referred', 'redirected', 'transferred'])
            ->orderBy('id', 'desc')
            ->value('referring_md');

        if (!$doctorId) {
            $doctorId = DB::table('tracking')->where('code', $code)->value('referring_md');
        }

        if (!$doctorId || !is_numeric($doctorId)) {
            return trim((string) $doctorId);
        }

        $doctor = DB::table('users')->where('id', $doctorId)->first();
        if (!$doctor) {
            return '';
        }

        return trim(ucwords(mb_strtolower($doctor->fname . ' ' . $doctor->lname)));
    }

    private function recoFacilities($code, $senderFacility)
    {
        if (!$code) {
            return [];
        }

        $facilities = DB::table('activity')
            ->where('code', $code)
            ->whereIn('status', ['referred', 'redirected', 'transferred'])
            ->select('referred_from', 'referred_to')
            ->get()
            ->flatMap(function ($activity) {
                return [$activity->referred_from, $activity->referred_to];
            })
            ->filter()
            ->map(function ($facilityId) {
                return (int) $facilityId;
            })
            ->unique()
            ->values()
            ->all();

        return array_values(array_diff($facilities, [(int) $senderFacility]));
    }

    private function recipients(array $facilityIds, $excludeUserId, $subopdId)
    {
        $users = DB::table('users')
            ->select('users.id', 'users.level', 'users.facility_id')
            ->whereRaw("LOWER(users.status) = 'active'")
            ->whereRaw("LOWER(users.level) NOT IN ('patient', 'support')")
            ->where(function ($query) use ($facilityIds) {
                $query->whereIn('users.facility_id', $facilityIds)
                    ->orWhereExists(function ($assignment) use ($facilityIds) {
                        $assignment->select(DB::raw(1))
                            ->from('facility_assignment')
                            ->whereRaw('facility_assignment.user_id = users.id')
                            ->whereRaw("LOWER(facility_assignment.status) = 'active'")
                            ->whereIn('facility_assignment.facility_id', $facilityIds);
                    });
            });

        if ($subopdId) {
            $users->where('users.subopd_id', $subopdId);
        }

        if ($excludeUserId) {
            $users->where('users.id', '!=', $excludeUserId);
        }

        return $users->distinct()->get()->all();
    }

    private function patientRecipients($code, $excludeUserId = null)
    {
        $users = DB::table('tracking')
            ->join('users', 'users.patient_id', '=', 'tracking.patient_id')
            ->select('users.id', 'users.level')
            ->where('tracking.code', $code)
            ->where('users.level', 'Patient')
            ->whereNotNull('users.patient_id')
            ->whereRaw("LOWER(users.status) = 'active'");

        if ($excludeUserId) {
            $users->where('users.id', '!=', $excludeUserId);
        }

        return $users->distinct()->get()->all();
    }

    private function destinationUrl(array $notification, $recipient)
    {
        if ($notification['type'] === 'reco_message' || strcasecmp($recipient->level, 'Patient') === 0) {
            return $notification['url'];
        }

        $code = rawurlencode($notification['code']);
        $filterRef = $notification['filter_ref'];

        if ($notification['referred_to'] && $this->recipientHasFacility($recipient, $notification['referred_to'])) {
            return url('doctor/referral?filterRef=' . $filterRef . '&search=' . $code);
        }

        if ($notification['referred_from'] && $this->recipientHasFacility($recipient, $notification['referred_from'])) {
            return url('doctor/referred?filterRef=' . $filterRef . '&referredCode=' . $code);
        }

        return $notification['url'];
    }

    private function recipientHasFacility($recipient, $facilityId)
    {
        if ((int) $recipient->facility_id === (int) $facilityId) {
            return true;
        }

        return DB::table('facility_assignment')
            ->where('user_id', $recipient->id)
            ->where('facility_id', $facilityId)
            ->whereRaw("LOWER(facility_assignment.status) = 'active'")
            ->exists();
    }
}