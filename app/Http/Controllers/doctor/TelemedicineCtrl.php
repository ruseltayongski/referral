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
        $appointment_schedule = AppointmentSchedule::orderBy('id', 'desc')->paginate(15);

        $data = [
            'appointment_schedule' => $appointment_schedule,
            'keyword' => $req->input('appt_keyword', ''),
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
        $appointment = new AppointmentSchedule($validateData);
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment created successfully');
    }

    public function updateAppointment(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer', // Adjust validation rules as needed
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

        $appointmentId = $validatedData['id'];
        $appointment = AppointmentSchedule::find($appointmentId);

        if (!$appointment) {
            // Handle the case where the appointment is not found
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

