<?php

namespace App\Http\Controllers\doctor;


use App\AppointmentSchedule;
use App\Tracking;
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
        $appointment_schedule = AppointmentSchedule::paginate(15);

        $data = [
            'appointment_schedule' => $appointment_schedule,
            'keyword' => $req->input('appt_keyword', ''), // Assuming you're using a keyword input
            'status' => $req->input('status_filter', ''),
            'date' => $req->input('date_filter', ''),
        ];

        return view('doctor.manage_appointment', $data);
    }

    public function appointmentCalendar() {
        return view('doctor.telemedicine_calendar');
    }

    public function createAppointment(Request $request)
    {
        //Validate the incoming request data
        $validateData = $request->validate([
           'appointed_date' => 'required|date',
           'appointed_time' => 'required',
           'created_by' => 'required',
           'facility_id' => 'required',
           'department_id' => 'required',
           'appointed_by' => 'required',
           'code' => 'required|max:255',
           'status' => 'required|max:255',
           'slot' => 'required|integer',
        ]);
        //Create a new AppointmentSchedule instance
        $appointment = new AppointmentSchedule($validateData);
        //Save the appointment to the database
        $appointment->save();
        //Redirect back or perform any other action
        return redirect()->back()->with('success', 'Appointment created successfully');
    }


    public function update(Request $request)
    {
        // Validate the request data as needed
        $validatedData = $request->validate([
            //'id' => 'required|integer', // Adjust validation rules as needed
            'appointed_date' => 'required|date',
            'appointed_time' => 'required',
            'created_by' => 'required',
            'facility_id' => 'required',
            'department_id' => 'required',
            'appointed_by' => 'required',
            'code' => 'required|max:255',
            'status' => 'required|max:255',
            'slot' => 'required|integer',
        ]);

        // Retrieve the appointment by ID
        $appointmentId = $validatedData['id'];
        $appointment = AppointmentSchedule::find($appointmentId);

        if (!$appointment) {
            // Handle the case where the appointment is not found
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Update the appointment data with the new values
        $appointment->appointed_date = $request->input('appointed_date');
        // Update other fields as needed

        // Save the updated appointment
        $appointment->save();

        // Return a success response
        return response()->json(['message' => 'Appointment updated successfully'], 200);
    }

    public function getAppointmentData($id)
    {
        // Debugging - Display the $id to see if it's received correctly
        //dd($id);

        // Retrieve the appointment by ID
        $appointment = AppointmentSchedule::find($id);

        // Debugging - Display $appointment to check if it's retrieved correctly
        //dd($appointment);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        // Return the appointment data as JSON
        return response()->json($appointment);
    }

}

