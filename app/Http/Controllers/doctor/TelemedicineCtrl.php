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

    public function editAppointment($id)
    {
        $appointment = AppointmentSchedule::find($id);
        if (!$appointment) {
            // Handle not found case, e.g., show an error message or redirect
        }

        return view('doctor.edit_appointment', compact('appointment'));
    }

    public function getEditAppointment($id)
    {
        $appointment = AppointmentSchedule::find($id);
        return view('doctor.edit_appointment_modal', compact('appointment'));
    }

    /*public function editAppointment($id)
    {
        $appointment = AppointmentSchedule::findOrFail($id);

        return view('edit_appointment', compact('appointment'));
    }*/

    public function updateAppointment(Request $request, $id)
    {
        $appointment = AppointmentSchedule::find($id);
        if (!$appointment) {
            // Handle not found case, e.g., show an error message or redirect
        }

        $validatedData = $request->validate([
            'appointed_date' => 'required|date',
            'appointed_time' => 'required',
            // Include other fields here
        ]);

        $appointment->update($validatedData);

        return redirect()->route('manage-appointment')->with('success', 'Appointment updated successfully');
    }


    public function deleteAppointment($id)
    {
        $appointment = AppointmentSchedule::find($id);
        if (!$appointment) {
            // Handle not found case, e.g., show an error message or redirect
        }

        $appointment->delete();

        return redirect()->route('manage-appointment')->with('success', 'Appointment deleted successfully');
    }
}