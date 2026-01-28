<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Login;
use App\Facility;
use App\AppointmentSchedule;
use App\TelemedAssignDoctor;
use App\Events\NewReferral;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\SubOpd;
use Carbon\Carbon;
use App\Activity;
use App\Patients;
use App\Province;
use App\Muncity;
use App\Barangay;
use App\Icd10;
use App\Tracking;
use App\Icd;
use App\Baby;
use App\PregnantForm;
use App\Department;
use App\PatientForm;
use App\ReasonForReferral;
use App\Http\Controllers\doctor\PatientCtrl;  

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

        return response()->json(['message' => 'Patient added successfully', 'data' => $data, 'address' => $patient_add, 'reason_for_referral' => self::getReasonForReferral()], 200);
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
    public function addTracking(
        $code,
        $patient_id,
        $user,
        array $json,
        $type,
        $form_id,
        $status = '',
        $telemed_assign_id = null
    ) {
        $subOPD_Id = isset($json['opdSubId']) ? (int)$json['opdSubId'] : 0;

        $referred_facility = $json['referred_facility'] ?? null;
        $referred_department = $json['referred_department'] ?? null;

        // Safety check
        if (!$referred_facility) {
            throw new \Exception('referred_facility is required for tracking.');
        }

        $track = [
            'code' => $code,
            'patient_id' => $patient_id,
            'ckd_id' => $json['ckd_id'] ?? null,
            'date_referred' => date('Y-m-d H:i:s'),
            'referred_from' => ($status == 'walkin') ? ($json['referring_facility_walkin'] ?? $user->facility_id) : $user->facility_id,
            'referred_to' => ($status == 'walkin') ? $user->facility_id : $referred_facility,
            'department_id' => $referred_department,
            'referring_md' => ($status == 'walkin') ? 0 : $user->id,
            'action_md' => $json['reffered_md'] ?? 0,
            'type' => $type,
            'form_id' => $form_id,
            'form_type' => 'version1',
            'remarks' => $json['reason'] ?? '',
            'status' => ($status == 'walkin') ? 'accepted' : 'referred',
            'walkin' => ($status == 'walkin') ? 'yes' : 'no',
            'telemedicine' => $json['telemedicine'] ?? 0,
            'subopd_id' => $subOPD_Id,
            'appointmentId' => $json['appointmentId'] ?? null,
        ];

        if ($status == 'walkin') {
            $track['date_seen'] = date('Y-m-d H:i:s');
            $track['date_accepted'] = date('Y-m-d H:i:s');
            $track['action_md'] = $user->id;
        }

        $tracking = Tracking::updateOrCreate(['code' => $code], $track);

        if ($telemed_assign_id) {
            $telemed_assign = TelemedAssignDoctor::find($telemed_assign_id);
            if ($telemed_assign) {
                $telemed_assign->tracking_id = $tracking->id;
                $telemed_assign->save();
            }
        }

        // Create Activity
        $activity = [
            'code' => $code,
            'patient_id' => $patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'date_seen' => ($status == 'walkin') ? date('Y-m-d H:i:s') : null,
            'referred_from' => ($status == 'walkin') ? ($json['referring_facility_walkin'] ?? $user->facility_id) : $user->facility_id,
            'referred_to' => ($status == 'walkin') ? $user->facility_id : $referred_facility,
            'sub_opdId' => $subOPD_Id,
            'department_id' => $referred_department,
            'referring_md' => ($status == 'walkin') ? 0 : $user->id,
            'action_md' => '',
            'remarks' => $json['reason'] ?? '',
            'status' => ($status == 'walkin') ? 'accepted' : 'referred',
        ];

        Activity::create($activity);

        if ($status == 'walkin') {
            $activity['remarks'] = 'Walk-In Patient';
            $activity['action_md'] = $user->id;
            Activity::create($activity);
        }

        // Websocket broadcast
        $patient = Patients::find($patient_id);
        $redirect_track = asset("doctor/referred?referredCode=") . $code;

        $new_referral = [
            "patient_name" => ucfirst($patient->fname) . ' ' . ucfirst($patient->lname),
            "referring_md" => ucfirst($user->fname) . ' ' . ucfirst($user->lname),
            "referring_name" => Facility::find($user->facility_id)->name ?? '',
            "referred_name" => Facility::find($referred_facility)->name ?? '',
            "referred_to" => (int)$referred_facility,
            "referred_department" => Department::find($referred_department)->description ?? '',
            "referred_from" => $user->facility_id,
            "form_type" => $type,
            "tracking_id" => $tracking->id,
            "referred_date" => date('M d, Y h:i A'),
            "patient_sex" => $patient->sex,
            "age" => ParamCtrl::getAge($patient->dob),
            "patient_code" => $code,
            "status" => "referred",
            "count_reco" => 0,
            "redirect_track" => $redirect_track,
            "position" => 0,
            "subOpdId" => $subOPD_Id,
            'telemedicine' => $json['telemedicine'] ?? 0,
        ];

        broadcast(new NewReferral($new_referral));
    }



   public function referPatient(Request $req, $type)
    {
        // Decode JSON payload manually
        $json = $req->all();
        // return $json;
        $user = (object) [
            'id' => $json['referring_md'] ?? null,
            'facility_id' => $json['referring_facility'] ?? null,
            'username' => $json['username'] ?? 'api_user'
        ];

        $referred_facility=isset($json['referred_facility']) ? (int) $json['referred_facility'] : null;
        $referred_department=isset($json['referred_department']) ? (int) $json['referred_department'] : null;
        $patient_id=isset($json['patient_id']) ? (int) $json['patient_id'] : null;


        // Validate required fields
        // if (!$user->id || !$user->facility_id || !$patient_id || !$referred_facility) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'referring_md, referring_facility, patient_id and referred_facility are required'
        //     ], 422);
        // }

        if (
            !isset($json['referring_md']) ||
            !isset($json['referring_facility']) ||
            !isset($json['patient_id']) ||
            !isset($json['referred_facility'])
        ) {
            return response()->json([
                'status' => false,
                'message' => 'referring_md, referring_facility, patient_id and referred_facility are required'
            ], 422);
        }


        $telemed_assigned_id = null;
        $referred_patient_data = null;

        DB::beginTransaction();

        try {

            /* =========================
            * TELEMEDICINE
            * ========================= */
            if (!empty($json['telemedicine']) && !empty($json['appointmentId'])) {
                $telemed_assigned = new TelemedAssignDoctor();
                $telemed_assigned->appointment_id = $json['appointmentId'];
                $telemed_assigned->subopd_id = $json['configId'] ?? null;
                $telemed_assigned->doctor_id = $user->id;
                $telemed_assigned->save();

                $telemed_assigned_id = $telemed_assigned->id;
            }

            $user_code = str_pad($user->facility_id, 3, '0', STR_PAD_LEFT);
            $code = date('ymd') . '-' . $user_code . '-' . date('His') . $user->facility_id . $user->id;
            $unique_id = $patient_id . '-' . $user->facility_id . '-' . date('ymdHis');

            if ($type === 'normal') {

                Patients::where('id', $patient_id)->update([
                    'sex'          => $json['patient_sex'] ?? null,
                    'civil_status' => $json['civil_status'] ?? null,
                    'phic_status'  => $json['phic_status'] ?? null,
                    'phic_id'      => $json['phic_id'] ?? null,
                ]);
               
                $form = PatientForm::create([
                    'unique_id'             => $unique_id,
                    'code'                  => $code,
                    'referring_facility'    => $user->facility_id,
                    'referred_to'           => $referred_facility,
                    'department_id'         => $referred_department,
                    'covid_number'          => $json['covid_number'] ?? null,
                    'refer_clinical_status' => $json['clinical_status'] ?? null,
                    'refer_sur_category'    => $json['sur_category'] ?? null,
                    'time_referred'         => date('Y-m-d H:i:s'),
                    'patient_id'            => $patient_id,
                    'case_summary'          => $json['case_summary'] ?? null,
                    'reco_summary'          => trim($json['reco_summary'] ?? ''),
                    'diagnosis'             => $json['diagnosis'] ?? null,
                    'referring_md'          => $user->id,
                    'referred_md'           => $json['reffered_md'] ?? $json['reffered_md_telemed'] ?? null,
                    'reason_referral'       => $json['reason_referral1'] ?? null,
                    'other_reason_referral' => $json['other_reason_referral'] ?? null,
                    'other_diagnoses'       => $json['other_diagnosis'] ?? null,
                ]);

                // return $form;

                /* =========================
                * FILE UPLOAD
                * ========================= */
                $file_paths = '';
                    if ($req->hasFile('file_upload')) {
                        $files = $req->file('file_upload');
                        foreach ($files as $file) {
                            $filename = $file->getClientOriginalName();
                            $file->storeAs('uploads/' . $user->username, $filename);
                            $file_paths .= ApiController::fileUploadUrl() . $user->username . '/' . $filename . '|';
                        }
                        $file_paths = rtrim($file_paths, '|');
                    }
                    $form->file_path = $file_paths;
                    $form->save();

                /* =========================
                * ICD CODES
                * ========================= */
                if (!empty($json['icd_ids']) && is_array($json['icd_ids'])) {
                    foreach ($json['icd_ids'] as $icd_id) {
                        $icd = new Icd();
                        $icd->code = $form->code;
                        $icd->icd_id = $icd_id;
                        $icd->save();
                    }
                }

                /* =========================
                * PUSH NOTIFICATION
                * ========================= */
                if ($referred_facility == 790 || $referred_facility == 23) {
                    $patient = Patients::find($patient_id);

                    $referred_patient_data = [
                        'age' => (string) ParamCtrl::getAge($patient->dob),
                        'chiefComplaint' => $json['case_summary'] ?? null,
                        'department' => Department::find($referred_department)->description ?? null,
                        'patient' => ucfirst($patient->fname) . ' ' .
                                    ucfirst($patient->mname) . ' ' .
                                    ucfirst($patient->lname),
                        'sex' => (string) $patient->sex,
                        'referring_hospital' => Facility::find($user->facility_id)->name ?? null,
                        'referred_to' => (string) $referred_facility,
                        'date_referred' => (string) $form->created_at,
                        'userid' => $user->id,
                        'patient_code' => $form->code,
                    ];

                    ApiController::notifierPushNotification($referred_patient_data);
                }

                self::addTracking(
                    $code,
                    $patient_id,
                    $user,
                    $json,
                    $type,
                    $form->id,
                    'refer',
                    $telemed_assigned_id
                );

                DB::commit();
                $patient_name = Patients::select('fname','mname','lname','contact')
                    ->where('id', $patient_id)->first();

                return response()->json([
                    'status'  => true,
                    'message' => 'Patient referred successfully',
                    'data'    => [
                        'form_id' => $form->id,
                        'code'    => $form->code,
                        'push'    => $referred_patient_data,
                        'patient_name' => ucfirst($patient_name->fname) . ' ' .
                                          ucfirst($patient_name->mname) . ' ' .
                                          ucfirst($patient_name->lname),
                    ],
                ], 201);
            }else if ($type === 'pregnant') {

                /* =========================
                * STORE BABY AS PATIENT
                * ========================= */
                $baby = [
                    'fname' => $json['baby_fname'] ?? '',
                    'mname' => $json['baby_mname'] ?? '',
                    'lname' => $json['baby_lname'] ?? '',
                    'dob'   => $json['baby_dob'] ?? '',
                    'civil_status' => 'Single'
                ];

                $baby_id = PatientCtrl::storeBabyAsPatient($baby, $patient_id);

                Baby::updateOrCreate(
                    [
                        'baby_id'   => $baby_id,
                        'mother_id' => $patient_id
                    ],
                    [
                        'weight'           => $json['baby_weight'] ?? '',
                        'gestational_age'  => $json['baby_gestational_age'] ?? '',
                        'birth_date'       => $json['baby_dob'] ?? ''
                    ]
                );

                /* =========================
                * CREATE PREGNANT FORM
                * ========================= */
                $form = PregnantForm::create([
                    'unique_id'                => $unique_id,
                    'code'                     => $code,
                    'referring_facility'       => $user->facility_id,
                    'referred_by'              => $user->id,
                    'record_no'                => $json['record_no'] ?? '',
                    'referred_date'            => date('Y-m-d H:i:s'),
                    'referred_to'              => $referred_facility,
                    'department_id'            => $referred_department,
                    'covid_number'             => $json['covid_number'] ?? null,
                    'refer_clinical_status'    => $json['clinical_status'] ?? null,
                    'refer_sur_category'       => $json['sur_category'] ?? null,

                    'patient_woman_id'         => $patient_id,
                    'woman_reason'             => $json['woman_reason'] ?? '',
                    'woman_major_findings'     => $json['woman_major_findings'] ?? '',
                    'woman_before_treatment'   => $json['woman_before_treatment'] ?? '',
                    'woman_before_given_time'  => $json['woman_before_given_time'] ?? '',
                    'woman_during_transport'   => $json['woman_during_treatment'] ?? '',
                    'woman_transport_given_time'=> $json['woman_during_given_time'] ?? '',
                    'woman_information_given'  => $json['woman_information_given'] ?? '',

                    'patient_baby_id'          => $baby_id,
                    'baby_reason'              => $json['baby_reason'] ?? '',
                    'baby_major_findings'      => $json['baby_major_findings'] ?? '',
                    'baby_last_feed'           => $json['baby_last_feed'] ?? '',
                    'baby_before_treatment'    => $json['baby_before_treatment'] ?? '',
                    'baby_before_given_time'   => $json['baby_before_given_time'] ?? '',
                    'baby_during_transport'    => $json['baby_during_treatment'] ?? '',
                    'baby_transport_given_time'=> $json['baby_during_given_time'] ?? '',
                    'baby_information_given'   => $json['baby_information_given'] ?? '',

                    'notes_diagnoses'          => $json['notes_diagnosis'] ?? '',
                    'reason_referral'          => $json['reason_referral1'] ?? '',
                    'other_reason_referral'    => $json['other_reason_referral'] ?? '',
                    'other_diagnoses'          => $json['other_diagnosis'] ?? '',
                ]);

                /* =========================
                * FILE UPLOAD
                * ========================= */
                $file_paths = '';
                if ($req->hasFile('file_upload')) {
                    foreach ($req->file('file_upload') as $file) {
                        $filename = $file->getClientOriginalName();
                        $file->storeAs('uploads/' . $user->username, $filename);
                        $file_paths .= ApiController::fileUploadUrl() . $user->username . '/' . $filename . '|';
                    }
                    $file_paths = rtrim($file_paths, '|');
                }
                $form->file_path = $file_paths;
                $form->save();

                /* =========================
                * ICD CODES
                * ========================= */
                if (!empty($json['icd_ids']) && is_array($json['icd_ids'])) {
                    foreach ($json['icd_ids'] as $icd_id) {
                        Icd::create([
                            'code'   => $form->code,
                            'icd_id' => $icd_id
                        ]);
                    }
                }

                /* =========================
                * PUSH NOTIFICATION
                * ========================= */
                if ($referred_facility == 790 || $referred_facility == 23) {
                    $patient = Patients::find($patient_id);

                    $referred_patient_data = [
                        'age' => (string) ParamCtrl::getAge($patient->dob),
                        'chiefComplaint' => $json['woman_major_findings'] ?? '',
                        'department' => Department::find($referred_department)->description ?? null,
                        'patient' => ucfirst($patient->fname).' '.ucfirst($patient->mname).' '.ucfirst($patient->lname),
                        'sex' => (string) $patient->sex,
                        'referring_hospital' => Facility::find($user->facility_id)->name ?? null,
                        'referred_to' => (string) $referred_facility,
                        'date_referred' => (string) $form->created_at,
                        'userid' => $user->id,
                        'patient_code' => $form->code
                    ];

                    ApiController::notifierPushNotification($referred_patient_data);
                }

                self::addTracking(
                    $code,
                    $patient_id,
                    $user,
                    $json,
                    $type,
                    $form->id,
                    'refer',
                    $telemed_assigned_id
                );

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Pregnant patient referred successfully',
                    'data'    => [
                        'form_id' => $form->id,
                        'code'    => $form->code,
                        'push'    => $referred_patient_data
                    ]
                ], 201);
            }

            // Invalid type
            return response()->json([
                'status'  => false,
                'message' => 'Invalid referral type',
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refer Patient API Error', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error while referring patient',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getReasonForReferral(){
        $reasons = ReasonForReferral::all();

        return response()->json([
            'reasons_for_referral' => $reasons
        ]);
    }

}
