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

use Illuminate\Support\Facades\Auth;

class TelemedicineCtrl extends Controller
{
    public function index(Request $req)
    {
        return view('doctor.video-call', ['referral_type'=>$req->form_type]);
    }

    public function manageAppointment(Request $req)
    {
        $facility = Session::get('auth')->facility_id;
       
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
                },
                'telemedAssignedDoctor' => function ($query) {
                    $query->with(['doctor' => function ($query) {
                        $query->select(
                            'id',
                            'fname',
                            'lname'
                        );
                    }]);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20); 
        
            $user_facility = User::where('department_id',5)
            ->where('level','doctor')
            ->where('facility_id',$facility)
            ->with('facility')
            ->groupBy('facility_id')
            ->get();

        $data = [
            'appointment_schedule' => $appointment_schedule,
            'facility' => $user_facility,
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
            if (empty($request['appointed_time'.$i]) || empty($request['appointed_time_to'.$i]) || empty($request->opdCategory.$i) || empty($request['available_doctor'.$i])) {
                continue;
            }

            $appointment_schedule = new AppointmentSchedule();
            $appointment_schedule->appointed_date = $request->appointed_date;
            $appointment_schedule->facility_id = $request->facility_id;
            $appointment_schedule->department_id = 5;
            $appointment_schedule->appointed_time = $request['appointed_time'.$i];
            $appointment_schedule->appointedTime_to = $request['appointed_time_to'.$i];
            $appointment_schedule->opdCategory = $request['opdCategory'.$i];
            //$appointment_schedule->slot = $request->slot.$i;
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

    //--------------------------------------------------
//     public function createAppointment(Request $request)
// {
//     $user = Session::get('auth');

//     // Track the ending time of the previous appointment
//     $prevTo = null;

//     // Iterate through each appointment
//     for ($i = 1; $i <= $request->appointment_count; $i++) {
//         // Check if any required field is empty, if so, skip this iteration
//         if (empty($request['appointed_time' . $i]) || empty($request['appointed_time_to' . $i]) || empty($request->opdCategory . $i) || empty($request['available_doctor' . $i])) {
//             continue;
//         }

//         // Validate appointment time 'from' and 'to'
//         $from = $request['appointed_time' . $i];
//         $to = $request['appointed_time_to' . $i];

//         if (strtotime($from) >= strtotime($to)) {
//             // Handle validation error - 'from' should be before 'to'
//             return redirect()->back()->withInput()->withErrors(['error' => 'Appointment time "from" should be before "to".']);
//         }

//         // Create a new AppointmentSchedule instance
//         $appointment_schedule = new AppointmentSchedule();
//         $appointment_schedule->appointed_date = $request->appointed_date;
//         $appointment_schedule->facility_id = $request->facility_id;
//         $appointment_schedule->department_id = 5;
//         $appointment_schedule->appointed_time = $from;
//         $appointment_schedule->appointedTime_to = $to;
//         $appointment_schedule->opdCategory = $request['opdCategory' . $i];
//         $appointment_schedule->created_by = $user->id;
//         $appointment_schedule->save();

//         // Save assigned doctors for this appointment
//         foreach ($request['available_doctor' . $i] as $doctorId) {
//             $tele_assign_doctor = new TelemedAssignDoctor();
//             $tele_assign_doctor->appointment_id = $appointment_schedule->id;
//             $tele_assign_doctor->doctor_id = $doctorId;
//             $tele_assign_doctor->created_by = $user->id;
//             $tele_assign_doctor->save();
//         }
        

//         // Update the ending time of the previous appointment
//         $prevTo = $to;
//     }

//     // Set session flag for successful appointment save
//     Session::put('appointment_save', true);

//     // Redirect back to previous page
//     return redirect()->back();
// }

    //--------------------------------------------------




    public function getAppointmentData(Request $request)
    {
        $appointment = AppointmentSchedule::find($request->id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
        return response()->json($appointment);
    }

    public function updateAppointment(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'appointed_date' => 'required|date',
            'appointed_time' => 'required',
            'appointedTime_to' => 'required',
            'facility_id' => 'required',
            'department_id' => 'required',
            'opdCategory' => 'required',
            'slot' => 'required|integer',
        ]);

        $appointmentId = $validatedData['id'];
        $appointment = AppointmentSchedule::find($appointmentId);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $appointment->fill($validatedData); // Update appointment attributes

        if ($appointment->save()) {
            return response()->json(['message' => 'Appointment updated successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to update appointment'], 500);
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
        $facility_data = AppointmentSchedule::where('facility_id', $request->id)->groupBy('appointed_date')->get();

        if (!$facility_data) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json(['facility_data' => $facility_data]);
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $timeSlots = AppointmentSchedule::
            with([
                'telemedAssignedDoctor' => function ($query) {
                    $query->with(['doctor' => function ($query) {
                        $query->select(
                            'id',
                            'fname',
                            'lname'
                        );
                    }]);
                }
            ])
            ->where('appointed_date', $request->selected_date)
            ->where('facility_id', $request->facility_id)
            ->get();

        return $timeSlots;
    }

    public function getDoctors($facility)
    {
        $doctors = User::where('facility_id', $facility)
                ->where('department_id','=',5)
                ->where('level', 'doctor')
                ->get(['id', 'fname', 'lname']);

        return $doctors;
    }

}

