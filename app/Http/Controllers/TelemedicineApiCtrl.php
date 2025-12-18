<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Login;
use App\Facility;
use App\AppointmentSchedule;
use App\TelemedAssignDoctor;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\SubOpd;
use Carbon\Carbon;
use App\Patients;
use App\Province;
use App\Muncity;
use App\Barangay;
use App\Icd10;

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
    public function getSubOPD($id){
        return SubOpd::select('description')
                ->where('id', $id)->first()->description ?: null;
    }
    public function countSlotTaken($appointment_id){
        return TelemedAssignDoctor::where('appointment_id', $appointment_id)
                ->count();
    }
    public function getDoctorName($id){
        return User::select('fname','mname','lname')
                    ->where('id', $id)->first();
    }
    public function getAppointmentDetails(Request $request)
    {
        $facility_id = $request->id;
        if (!$facility_id) {
            return response()->json(['error' => 'facility id required'], 400);
        }

        // parse exclude_past query param (default true)
        $excludePastParam = $request->query('exclude_past', '1'); // accept '1','0','true','false'
        $excludePast = filter_var($excludePastParam, FILTER_VALIDATE_BOOLEAN);

        $now = Carbon::now()->toDateTimeString();

        $query = AppointmentSchedule::where('facility_id', $facility_id);

        if ($excludePast) {
            $query->whereRaw('TIMESTAMP(appointed_date, appointed_time) > ?', [$now]);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            return response()->json(['facility_data' => []], 200);
        }

        $schedules = $schedules->filter(function ($s) {
            return ($s->slot - self::countSlotTaken($s->id)) > 0;
        });

        $data = $schedules->map(function ($s) {  
            $doctorName = self::getDoctorName($s->created_by);
                return [
                    'id' => $s->id,
                    'configID' => $s->configId ?? null,
                    'appointed_date' => $s->appointed_date,
                    'date_end' => $s->date_end,
                    'appointed_time' => $s->appointed_time,
                    'appointedTime_to' => $s->appointedTime_to,
                     'doctor_name' => 'Dr.' 
                    . $doctorName->fname . ' ' 
                    . $doctorName->mname . ' ' 
                    . $doctorName->lname,
                    'created_by' => $s->created_by,
                    'facility_id' => $s->facility_id,
                    'department_id' => $s->department_id,
                    'opdCategory' => $s->opdCategory,
                    'subOPDdescription' => self::getSubOPD($s->opdCategory),
                    'code' => $s->code,
                    'status' => $s->status,
                    'slot' => $s->slot,
                    'slot_available' => $s->slot - self::countSlotTaken($s->id),
                ];
           
        })->groupBy('appointed_date')->map(function ($group) {
            return $group->values();
        })->toArray();

        return response()->json(['facility_data' => $data]);
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
        $groupedByDeptAndDate = [];
        
        foreach ($appointment_slot as $slot) {
            $facility_id = $slot->id;
            $facility_name = $slot->name;
            $facility_address = $slot->address ?? 'N/A'; 
            $schedules = $slot->appointmentSchedules;

            $availableSlot = 0;
            $totalAppointments = 0;

            foreach ($schedules as $schedule) {
                $deptName = $schedule->subOpd->description;
                $dateKey = $schedule->appointed_date;
                $key = $deptName.'_'.$dateKey;

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

                if (!isset($groupedByDeptAndDate[$key])) {
                $groupedByDeptAndDate[$key] = [
                        'facility_id' => $facility_id,
                        'department' => $deptName,
                        'date' => $dateKey,
                        'slots' => []
                    ];
                }

                // APPEND to the slots array instead of overwriting
                $groupedByDeptAndDate[$key]['slots'][] = [
                    'appointed_time' => $schedule->appointed_time,
                    'appointedTime_to' => $schedule->appointedTime_to,
                    'created_by' => $schedule->createdBy
                        ? [
                            'fname' => $schedule->createdBy->fname,
                            'lname' => $schedule->createdBy->lname
                        ]
                        : null,
                    'telemed_assigned_doctor' => $schedule->telemedAssignedDoctor,
                    'opdCategory' => $schedule->opdCategory,
                    'department_id' => $schedule->sub_opd_id,
                    'slot' => (int)$schedule->slot - $schedule->telemedAssignedDoctor->count()
                ];
            }
            
            // Add facility summary
            if ($availableSlot > 0){
                $slotCountByFacility[] = [
                                'facility_id' => $facility_id,
                                'facility_name' => $facility_name,
                                'facility_address' => $facility_address,
                                'available_slot' => $availableSlot,
                                'total_appointments' => $totalAppointments,
                            ];
            }
        }

        return response()->json([
            'slotCountByFacility' => $slotCountByFacility,
            'groupedByDeptAndDate' => array_values($groupedByDeptAndDate)
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

    public function storePatient(Request $req)
    {
        // FIX: Read JSON from mobile app
        $dataReq = json_decode($req->getContent(), true);

        // Build unique ID
        $unique = array(
            $dataReq['fname'],
            $dataReq['mname'],
            $dataReq['lname'],
            date('Ymd', strtotime($dataReq['dob'])),
            $dataReq['brgy']
        );
        $unique = implode($unique);

        $match = array('unique_id' => $unique);

        $data = array(
            'phic_status' => $dataReq['phic_status'],
            'phic_id'     => isset($dataReq['phicID']) ? $dataReq['phicID'] : '',
            'fname'       => $dataReq['fname'],
            'mname'       => $dataReq['mname'],
            'lname'       => $dataReq['lname'],
            'contact'     => $dataReq['contact'],
            'dob'         => $dataReq['dob'],
            'sex'         => $dataReq['sex'],
            'civil_status'=> $dataReq['civil_status'],
            'region'      => $dataReq['region'],
            'province'    => $dataReq['province'],
            'muncity'     => $dataReq['muncity'],
            'brgy'        => $dataReq['brgy'],
            'province_others' => $dataReq['province_others'],
            'muncity_others'  => $dataReq['muncity_others'],
            'brgy_others'     => $dataReq['brgy_others']
        );

        $data = Patients::updateOrCreate($match, $data);
        $patient_add = implode(', ', array_filter([
            $dataReq['region'] ?? '',
                Province::where('id', $dataReq['province'])->value('description'),
                Muncity::where('id', $dataReq['muncity'])->value('description'),
                Barangay::where('id', $dataReq['brgy'])->value('description'),
        ]));

        // Save to session
        Session::put('profileSearch', [
            'keyword' => $dataReq['fname'] . ' ' . $dataReq['lname'],
            'region' => $dataReq['region'],
            'province' => $dataReq['province'],
            'muncity' => $dataReq['muncity'],
            'brgy' => $dataReq['brgy']
        ]);

        // If sent from consultation
        if (!empty($dataReq['from_consultation'])) {
            return response()->json(['message' => 'Patient added successfully', 'data' => $data, 'address' => $patient_add], 200);
        }

        return response()->json(['message' => 'Patient added successfully', 'data' => $data, 'address' => $patient_add], 200);
    }

 
    public function searchIcd10(Request $request, $keyword){
        if(!$keyword){
            return response()->json([]);
        }

        $icd = Icd10::where("description","like","%$keyword%")
                    ->orWhere("code","like","%$keyword%")
                    ->get();

        return response()->json([
            'icd' => $icd,
            'icd_keyword' => $keyword
        ]);
    }

}
