<?php

namespace App\Http\Controllers\doctor;

use App\AppointmentSchedule;
use App\Department;
use App\Facility;
use App\TelemedAssignDoctor;
use App\Tracking;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class TelemedicineCtrl extends Controller
{
    public function index(Request $req)
    {
        return view('doctor.video-call', ['referral_type'=>$req->form_type]);
    }

    public function manageAppointment(Request $req)
    {
        $page = $req->input('page', 1);
        $perPage = 10;

        $appointment_schedule = AppointmentSchedule::
            with([
                'createdBy' => function ($query) {
                    $query->select(
                        'id',
                        'username'
                    );
                },
                'facility' => function ($query) {
                    $query->select(
                        'id',
                        'name'
                    );
                },
                'department' => function ($query) {
                    $query->select(
                        'id',
                        'description'
                    );
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $user_facility = User::
            with([
                'department' => function ($query) {
                    $query->select(
                        'id',
                        'description'
                    );
                },
                'facility' => function ($query) {
                    $query->select(
                        'id',
                        'name'
                    );
                }
            ])
            ->where('department_id',"=", '5')
            ->where('level',"=", 'doctor')
            ->groupBy('facility_id')
            ->get();

        $data = [
            'appointment_schedule' => $appointment_schedule,
            'facility' => $user_facility,
            'facilityList' => Facility::all(),
            'departmentList' => Department::all(),
            'keyword' => $req->input('appt_keyword', ''),
            'status' => $req->input('status_filter', ''),
            'date' => $req->input('date_filter', ''),
        ];
        return view('doctor.manage_appointment', $data);
    }

    public function appointmentCalendar() {
        $user = Session::get('auth');
        $appointment_sched = AppointmentSchedule::select("appointment_schedule.*",DB::raw("sum(appointment_schedule.slot) as slot"))->groupBy('appointment_schedule.facility_id')->with('facility')->get();
        return view('doctor.telemedicine_calendar1',[
            'appointment_sched' => $appointment_sched,
            'user' => $user
        ]);
    }

    public function createAppointment(Request $request)
    {
        $user = Session::get('auth');
        for($i=1; $i<=$request->appointment_count; $i++) {
            
            $appointment_schedule = new AppointmentSchedule();
            $appointment_schedule->appointed_date = $request->appointed_date;
            $appointment_schedule->facility_id = $request->facility_id;
            $appointment_schedule->department_id = 5;
            $appointment_schedule->appointed_time = $request->input("appointed_time{$i}");
            $appointment_schedule->appointedTime_to = $request->input("appointed_time_to{$i}");
            $appointment_schedule->opdCategory = $request->input("opdCategory{$i}");
            $appointment_schedule->slot = $request->input("slot{$i}");
            $appointment_schedule->created_by = $user->id;
            $appointment_schedule->save();
            for($x=0; $x<count($request['available_doctor'.$i]); $x++) {
                $tele_assign_doctor = new TelemedAssignDoctor();
                $tele_assign_doctor->appointment_id = $appointment_schedule->id;
                $tele_assign_doctor->doctor_id = $request['available_doctor'.$i][$x];
                $tele_assign_doctor->created_by = $user->id;
                $tele_assign_doctor->save();
            }
        }

        Session::put('appointment_save',true);
        return redirect()->back();
    }

    public function getAppointmentData(Request $request)
    {
        //dd($id);
        // $doctor_id =TelemedAssignDoctor::where('appointment_id', $request->id)
        // ->pluck('doctor_id')->first();
        // $appointment = AppointmentSchedule::find($request->id);
        $appointed_date = AppointmentSchedule::where('id', $request->id)
        ->pluck('appointed_date')
        ->first();
        $appointment = AppointmentSchedule::where('appointed_date', $appointed_date)->
            with([
                'createdBy' => function ($query) {
                    $query->select(
                        'id',
                        'username'
                    );
                },
                'facility' => function ($query) {
                    $query->select(
                        'id',
                        'name'
                    );
                },
                'department' => function ($query) {
                    $query->select(
                        'id',
                        'description'
                    );
                },
                'telemedAssignDoctor.user' => function ($query){
                    $query->select('id', 'username');
                },
            ])->get();
          
 
        return response()->json($appointment);

    }

    public function updateAppointment(Request $request)
    {

        for($i=1; $i<=$request->appointment_count; $i++) {
      dd(
        $appointedDate = $request->input("update_appointed_date"),
        $facility_id = $request->input("update_facility_id"),
        $time_from = $request->input("update_appointed_time{$i}"),
        $department = $request->input("update_department_id"),
        $time_from = $request->input("appointment_count")
      );
        // $data = [
        //    'appointmentDate' => $appointedDate,
        //    'facility_id' =>$facility_id,
        //    'department' => $department,
        //    'time_from' => $time_from,
        // ];
        //  return response()->json($data);
      }
    }

    public function deleteAppointment(Request $request)
    {
        $id = $request->input('id');
        $appointment = AppointmentSchedule::find($id);

        if($appointment->delete()){
            return response()->json(['success' => 'Appointment deleted successfully']);
        } else {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
    }

    public function getUserData($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function getFacilityDetails(Request $request)
    {
        $facility_data = AppointmentSchedule::where('facility_id', $request->id)->get();

        if (!$facility_data) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json(['facility_data' => $facility_data]);
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $date = $request->input('selected_date');

        // Fetch available time slots based on the selected date
        $timeSlots = AppointmentSchedule::select('appointed_time','appointedTime_to','appointed_date')
            ->where('appointed_date', $date)
            ->get();

        return response()->json(['time_slots' => $timeSlots]);
    }

    public function getDoctors($facilityId)
    {
        $doctors = User::where('facility_id', $facilityId)
                ->where('department_id','=',5)
                ->where('level', 'doctor')
                ->get(['id', 'fname', 'lname']);

        return $doctors;
    }

   /* public function getFacilitiesByDepartmentAndType($departmentId) {
        // Assuming Facility and Department models with relationships

        // Fetch facilities based on department ID and type
        $facilities = Facility::where('department_id', $departmentId)
            ->where(function($query) {
                $query->where('type', 'OPD')
                    ->orWhere('type', 5);
            })
            ->get();

        return response()->json($facilities);
    }*/



}

