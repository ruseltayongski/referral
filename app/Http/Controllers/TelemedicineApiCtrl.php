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

        // Get all facility IDs that have appointment schedules
        $facility_ids = AppointmentSchedule::pluck('facility_id')->unique();

        // Fetch facilities with their appointment schedules and relations
        $appointment_slot = Facility::with([
            'appointmentSchedules.telemedAssignedDoctor',
            'appointmentSchedules.configSchedule',
            'appointmentSchedules.subOpd'
        ])
        ->whereHas('appointmentSchedules', function($q) use ($user) {
            $q->where('facility_id', '!=', $user->facility_id);
        })
        ->findMany($facility_ids);

        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $slotCountByFacility = [];

        foreach ($appointment_slot as $slot) {
            $facility_id = $slot->id;
            $facility_name = $slot->name;
            $facility_address = $slot->address ?? 'N/A'; 
            $schedules = $slot->appointmentSchedules;

            $availableSlot = 0;
            $totalAppointments = 0;

            foreach ($schedules as $schedule) {
                $scheduleDateTime = \Carbon\Carbon::parse($schedule->appointed_date . ' ' . $schedule->appointed_time);
                $countSlot = $schedule->slot ?? 0;
                $assignedDoctorsCount = $schedule->telemedAssignedDoctor ? $schedule->telemedAssignedDoctor->count() : 0;

                $isCurrentMonth = (
                    $scheduleDateTime->month === $currentMonth &&
                    $scheduleDateTime->year === $currentYear
                );

                if ($isCurrentMonth) {
                    // Add appointments based on assigned doctors
                    if ($assignedDoctorsCount > 0) {
                        $totalAppointments += $assignedDoctorsCount;
                    }

                    // Add missed or past unassigned slots (as in frontend logic)
                    if ($scheduleDateTime->isPast()) {
                        $totalAppointments += ($countSlot - $assignedDoctorsCount);
                    }
                }

                // Available slot logic (future schedule and not full)
                if ($scheduleDateTime->isFuture() && $assignedDoctorsCount < $countSlot) {
                    $availableSlot += ($countSlot - $assignedDoctorsCount);
                }
            }

            // Add facility summary
            $slotCountByFacility[] = [
                'facility_id' => $facility_id,
                'facility_name' => $facility_name,
                'facility_address' => $facility_address,
                'available_slot' => $availableSlot,
                'total_appointments' => $totalAppointments,
            ];
        }

        return response()->json([
            'slotCountByFacility' => $slotCountByFacility
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
