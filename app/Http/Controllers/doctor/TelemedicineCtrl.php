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

        $data = [
            'appointment_schedule' => $appointment_schedule,
            'facility' => Facility::all(),
            'facilityList' => Facility::all(),
            'departmentList' => Department::all(),
            'doctors' => User::get(),
            'keyword' => $req->input('appt_keyword', ''),
            'status' => $req->input('status_filter', ''),
            'date' => $req->input('date_filter', ''),
        ];
        return view('doctor.manage_appointment', $data);
    }

    function departmentGet(Request $request){
        $users = User::with(['facility', 'department'])
            ->where('facility_id', $request->facility_id)
            ->get();


//        $facilityId = $request->facility_id;
//        // Retrieve users with the specified facility and 'OPD' department value
//        $users = User::where('facility_id', $facilityId)
//            ->whereHas('department', function ($query) {
//                $query->where('description', 'OPD');
//            })
//            ->with(['facility', 'department'])
//            ->get();

        return $users;
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
        $validateData = $request->validate([
           'appointed_date' => 'required|date',
           'appointed_time' => 'required',
           'appointedTime_to' => 'required',
           'facility_id' => 'required',
           'department_id' => 'required',
           'opdCategory' => 'required',
           'slot' => 'required|integer',
        ]);
        $user = Session::get('auth');
        $validateData['created_by'] = $user->id;

        $appointment = new AppointmentSchedule($validateData);
        $appointment->save();

        //------------------------------------------------------------------
        // Create a new TelemedAssignDoctor instance and save the relationship
        $telemedAssignDoctor = new TelemedAssignDoctor([
            'appointment_id' => $appointment->id,
            'doctor_id' => $user->id, // Modify this based on your actual structure
            'status' => 'pending', // Set an appropriate status
            'created_by' => $user->id,
        ]);

        $telemedAssignDoctor->save();
        //------------------------------------------------------------------


        return redirect()->back()->with('success', 'Appointment created successfully');
    }

    public function getAppointmentData(Request $request)
    {
        //dd($id);
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
        $facility_data = AppointmentSchedule::where('facility_id', $request->id)->get();

        if (!$facility_data) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        /*return response()->json($facility_data);*/
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


    public function getDoctors($departmentId)
    {
        // Assuming 'level' column represents the user type
        $doctors = User::where([
            ['level', '=', 'doctor'],
            ['department_id', '=', $departmentId],
        ])->get(['id', 'username']);

        return response()->json($doctors);
    }



}

