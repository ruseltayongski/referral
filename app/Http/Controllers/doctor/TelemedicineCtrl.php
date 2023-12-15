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

    public function departmentGet(Request $request){
//        $users = User::with(['facility', 'department'])
//            ->where('facility_id', $request->facility_id)
//            ->get();

//        $facilityId = $request->facility_id;
//        // Retrieve users with the specified facility and 'OPD' department value
//        $users = User::where('facility_id', $facilityId)
//            ->whereHas('department', function ($query) {
//                $query->where('description', 'OPD');
//            })
//            ->with(['facility', 'department'])
//            ->get();
//
//        return $users;
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

        // Set the department to "OPD"
        $validateData['department_id'] = 5;

        $appointment = new AppointmentSchedule($validateData);
        $appointment->save();

        $selectedDoctors = $request->available_doctor;

        //------------------------------------------------------------------
        // Create a new TelemedAssignDoctor instance and save the relationship
        $telemedAssignDoctor = new TelemedAssignDoctor([
            'appointment_id' => $appointment->id,
            'doctor_id' => $user->id, // Modify this based on your actual structure
            'status' => 'pending', // Set an appropriate status
            'created_by' => $user->id,
        ]);

        $telemedAssignDoctor->save();

        try {
            foreach ($selectedDoctors as $doctorId) {
                TelemedAssignDoctor::create([
                    'appointment_id' => $appointment->id,
                    'doctor_id' => $doctorId,
                    'status' => 'pending',
                    'created_by' => $user->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error saving data to TelemedAssignDoctor: ' . $e->getMessage());
            // Handle the error as needed
        }
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

    public function getDoctors($facilityId)
    {
        try {
            // Assuming 'level' is a column in the users table
            $doctors = User::where('facility_id', $facilityId)
                ->where('department_id','=',5)
                ->where('level', 'doctor')
                ->get();

            // You can return the doctors as JSON or in any other format you prefer
            return $doctors;
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            \Log::error("Error fetching doctors: " . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Failed to fetch doctors'], 500);
        }

    }

    public function getFacilitiesByDepartmentAndType($departmentId) {
        // Assuming Facility and Department models with relationships

        // Fetch facilities based on department ID and type
        $facilities = Facility::where('department_id', $departmentId)
            ->where(function($query) {
                $query->where('type', 'OPD')
                    ->orWhere('type', 5);
            })
            ->get();

        return response()->json($facilities);
    }



}

