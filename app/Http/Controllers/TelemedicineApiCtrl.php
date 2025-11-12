<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Login;
use App\Facility;
use App\AppointmentSchedule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TelemedicineApiCtrl extends Controller
{
    public function login(Request $req)
    {
        $login = User::where('username', $req->username)->first();
        $last_login = now(); // current timestamp

        if ($login && Hash::check($req->password, $login->password)) {

            User::where('id', $login->id)->update([
                'last_login' => $last_login,
                'login_status' => 'login'
            ]);

            $checkLastLogin = self::checkLastLogin($login->id);

            $l = new Login();
            $l->userId = $login->id;
            $l->login = $last_login;
            $l->logout = "0000-00-00 00:00:00";
            $l->status = 'login';
            $l->type = $req->login_type;
            $l->login_link = $req->login_link;
            $l->save();

            if ($checkLastLogin > 0) {
                Login::where('id', $checkLastLogin)->update([
                    'logout' => $last_login
                ]);
            }

            if ($login->level == 'doctor') {
                return response()->json([
                    'id' => $login->id,
                    'data' => $login
                ]);
            } else {
                return response()->json(['message' => 'unauthorized access'], 403);
            }
        } else {
            return response()->json(['message' => 'invalid credentials'], 401);
        }
    }

    public function appointmentCalendar()
    {
        $user = Session::get('auth');

        // Get all facility IDs from appointment schedules
        $facility_ids = AppointmentSchedule::pluck('facility_id')->unique();

        // Load facilities with related appointment schedules + config
        $facilities = Facility::with([
            'appointmentSchedules.telemedAssignedDoctor',
            'appointmentSchedules.configSchedule',
            'appointmentSchedules.subOpd'
        ])
        ->whereHas('appointmentSchedules', function($q) use ($user) {
            $q->where('facility_id', '!=', $user->facility_id);
        })
        ->whereIn('id', $facility_ids)
        ->get();

        // $pastAppointments = [];
        // $upcomingAppointments = [];
        $slotCountByFacility = [];

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        foreach ($facilities as $facility) {
            $facilitySlotCount = 0;
            $availableSlot = 0;

            foreach ($facility->appointmentSchedules as $schedule) {
                $appointmentDateTime = Carbon::parse($schedule->appointed_date . ' ' . $schedule->appointed_time);

                // $appointmentData = [
                //     'id' => $schedule->id,
                //     'appointed_date' => $schedule->appointed_date,
                //     'appointed_time' => $schedule->appointed_time,
                //     'facility_id' => $schedule->facility_id,
                //     'facility_name' => $facility->name,
                //     'address' => $facility->address,
                //     'slot' => $schedule->slot,
                // ];

                // Categorize appointments
                // if ($appointmentDateTime->lt($now)) {
                //     $pastAppointments[] = $appointmentData;
                // } else {
                //     $upcomingAppointments[] = $appointmentData;
                // }

                // Count slots within current month
                if ($appointmentDateTime->between($monthStart, $monthEnd)) {
                    $facilitySlotCount += (int) $schedule->slot;
                }

                // If configSchedule has a max slot count, add that to availableSlot
                if ($schedule->configSchedule && isset($schedule->configSchedule->max_slot)) {
                    $availableSlot += (int) $schedule->configSchedule->max_slot;
                }
            }

            // Compute remaining available slots (if total available > booked)
            $remainingSlot = $availableSlot > 0 ? max($availableSlot - $facilitySlotCount, 0) : 0;

            // Save slot summary for each facility
            $slotCountByFacility[] = [
                'facility_id' => $facility->id,
                'facility_name' => $facility->name,
                'facility_address' => $facility->address,
                'slot_count_this_month' => $facilitySlotCount,
                'available_slot' => $remainingSlot,
            ];
        }

        return response()->json([
            // 'pastAppointments' => $pastAppointments,
            // 'upcomingAppointments' => $upcomingAppointments,
            'slotCountByFacility' => $slotCountByFacility,
        ]);
    }



    public function checkLastLogin($id)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $login = Login::where('userId', $id)
            ->whereBetween('login', [$start, $end])
            ->orderBy('id', 'desc')
            ->first();

        if ($login && !($login->logout >= $start && $login->logout <= $end)) {
            return true;
        }

        if (!$login) {
            return false;
        }

        return $login->id;
    }


}
