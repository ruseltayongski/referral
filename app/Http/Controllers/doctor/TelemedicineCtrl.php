<?php

namespace App\Http\Controllers\doctor;


use App\AppointmentSchedule;
use App\Department;
use App\Facility;
use App\Tracking;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TelemedicineCtrl extends Controller
{
    public function index(Request $req)
    {
        return view('doctor.video-call', ['referral_type'=>$req->form_type]);
    }

    public function manageAppointment(Request $req)
    {
        // Get the current page from the request, default to 1 if not provided
        $page = $req->input('page', 1);

        // Define the number of items per page
        $perPage = 10; // You can adjust this as needed

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
            'keyword' => $req->input('appt_keyword', ''),
            'status' => $req->input('status_filter', ''),
            'date' => $req->input('date_filter', ''),
        ];
        return view('doctor.manage_appointment', $data);
    }

    /*--------------------------------------------------*/

    function departmentGet(Request $request){
        $users = User::with(['facility', 'department'])
            ->where('facility_id', $request->facility_id)
            ->get();

        return $users;
    }
    /*--------------------------------------------------*/

    public function appointmentCalendar() {
        return view('doctor.telemedicine_calendar');
    }

    public function createAppointment(Request $request)
    {
         /*'appointed_by' => 'required',
           'code' => 'required|max:255',
           'status' => 'required|max:255',*/
        //dd($request->all()); // Dump the form data

        $validateData = $request->validate([
           'appointed_date' => 'required|date',
           'appointed_time' => 'required',
           'facility_id' => 'required',
           'department_id' => 'required',
           'slot' => 'required|integer',
        ]);
        //dd($userId);
        $user = Session::get('auth');
        $validateData['created_by'] = $user->id;

        $appointment = new AppointmentSchedule($validateData);
        $appointment->save();
        return redirect()->back()->with('success', 'Appointment created successfully');
        /*--------------------------------------------------------------------------------*/
    }

    public function updateAppointment(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer', // Adjust validation rules as needed
            'appointed_date' => 'required|date',
            'appointed_time' => 'required',
            /*'created_by' => 'required',*/
            'facility_id' => 'required',
            'department_id' => 'required',
            /*'appointed_by' => 'required',
            'code' => 'required|max:255',
            'status' => 'required|max:255',*/
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

    public function getAppointmentData($id)
    {
        // Debugging - Display the $id to see if it's received correctly
        //dd($id);
        $appointment = AppointmentSchedule::find($id);

        // Debugging - Display $appointment to check if it's retrieved correctly
        //dd($appointment);
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
        return response()->json($appointment);
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
}

