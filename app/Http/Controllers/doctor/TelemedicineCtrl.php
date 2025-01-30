<?php

namespace App\Http\Controllers\doctor;

use App\AppointmentSchedule;
use App\Department;
use App\Facility;
use App\TelemedAssignDoctor;
use App\Tracking;
use App\User;
use App\Cofig_schedule;
use App\SubOpd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Prophecy\Exception\Doubler\ReturnByReferenceException;

class TelemedicineCtrl extends Controller
{
    public function index(Request $req)
    {
        return view('doctor.video-call', ['referral_type'=>$req->form_type]);
    }

    public function manageAppointment(Request $req)
    {
        $user = Session::get('auth');
        $facility = $user->facility_id;
        $config_sched = Cofig_schedule::select('id','department_id','facility_id','subopd_id','created_by','description','category','days','time')->get();
        $query = AppointmentSchedule::
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
                },
                'subOpd',
                'configSchedule',
            ])
            ->where('opdCategory', $user->subopd_id)
            ->where('facility_id',$facility);
        

        $appointment_schedule = $query->orderBy('created_at', 'desc')
            //->where('status', $appointmentstatus)
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
            'configs' => $config_sched,
            'appointment_filter' => $appointmentstatus
        ];

        return view('doctor.manage_appointment', $data);
    }

    public function configSched(){
        $user = Session::get('auth');
        $department = Department::all();
        $config_sched = Cofig_schedule::select('id','department_id','subopd_id','facility_id','created_by','description','category','days','time')
                        ->where('facility_id', $user->facility_id)
                        ->get();
        
        $facility = Facility::select('id','name')->where('id', $user->facility_id)->get();
        
        return view('doctor.config_schedule',[
            'department' => $department,
            'fact' => $facility,
            'config' => $config_sched
        ]);
    }

    public function getConfig($id) {

       $getconfig = Cofig_schedule::find($id);
        
       $timesRaw = explode('|', $getconfig->time);
       $days =  explode('|', $getconfig->days);

       $times = [];
       $currentDay = null;

        foreach($timesRaw as $time){

            if(preg_match('/^[A-Za-z]+$/', $time)){
                
                $currentDay = $time;
                
                if(!isset($times[$currentDay])){

                    $times[$currentDay] = [];
                } 
            }elseif ($currentDay){
                $times[$currentDay][] = $time;
            }

        }

       return response()->json([
            'times' => $times, 
            'days' => $days,
            'descript' => $getconfig->description,
            'deparment_subcategory' => $getconfig->subopd_id,
            'category' => $getconfig->category,
            'configId' => $getconfig->id,
            'subOpdId' => $getconfig ->subopd_id
        ]);
    }

    public function getdoctorconfig($id){
        $configData = Cofig_schedule::select('id', 'days', 'time', 'category')
            ->with(['appointmentSchedules' => function ($query) {
                $query->select('id', 'configId', 'opdCategory', 'appointed_date', 'date_end');
            }])
            ->find($id);

            return response()->json([
                'status' => 'success',
                'timeSlotData' => $configData
            ]);
    }

    // public function deleteSchedule($schedId,$configId){

    //     return response()->json([
    //         'schedId' => $schedId,
    //         'configId' => $configId
    //     ]);
    // }

    public function AddconfigSched(Request $req){
       $user = Session::get('auth');
    //   dd($req->all());
       $config_sched = new Cofig_schedule();

       $config_sched->description = $req->configdesc;
       $config_sched->department_id = $req->department_id;
       $config_sched->category = $req->default_category;
       $config_sched->facility_id = $req->facility_id;
       $config_sched->subopd_id = $req->subOpd_id;
       $scheduleData = [];

       foreach($req->days as $day){
            
            if(isset($req->time_from[$day]) && isset($req->time_to[$day])){

                $timeSlots = [];
                foreach($req->time_from[$day] as $index => $timeFrom){
                    $timeTo = $req->time_to[$day][$index] ?? '';
                    $timeSlots[] = "{$timeFrom}-{$timeTo}";
                }

                $scheduleData[] = "{$day}|" . implode('|', $timeSlots);
            }
       }

       $config_sched->days = implode('|', $req->days);
       $config_sched->time = implode('|', $scheduleData);
       $config_sched->created_by = $user->id;
       $config_sched->save();

       return redirect::back();
    }

    public function editconfigSched(Request $req){
        // dd($req->all());
        $user = Session::get('auth');
        $config_sched = Cofig_schedule::where('id', $req->edit_configId)->first();
        $config_sched->description = $req->configdesc;
        $config_sched->department_id = $req->department_id;
        $config_sched->category = $req->default_category;
        $config_sched->facility_id = $req->facility_id;
        $config_sched->subopd_id = $req->subopd_id;
        $scheduleData = [];

        foreach($req->days as $day){
            $timeSlots = [];

            if (!empty($req->update_time_from[$day]) && !empty($req->update_time_to[$day])) {
 
                 foreach($req->update_time_from[$day] as $index => $timeFrom){
                     $timeTo = $req->update_time_to[$day][$index] ?? '';

                     if(!empty($timeFrom) && !empty($timeTo)){

                        $timeSlots[] = "{$timeFrom}-{$timeTo}";
                     }
                 }
            }

            if(!empty($timeSlots)){
                $scheduleData[] = "{$day}|" . implode('|', $timeSlots);
            }else{

            }
        }


        $config_sched->days = implode('|', $req->days);
        $config_sched->time = implode('|', $scheduleData);
        $config_sched->created_by = $user->id;
        $config_sched->save();

        return redirect::back();
        
    }

    public function updateDoctorConfig(Request $req){
        $user = Session::get('auth');
          //dd($req->all());
         $appointedConfig = AppointmentSchedule::where('id', $req->Appointment_schedule_id)->first();
         $appointedConfig->configId = $req->edit_config_id;

         try {
            $startDate = Carbon::createFromFormat('m-d-Y', $req->startDate);
            $endDate = Carbon::createFromFormat('m-d-Y', $req->endDate);
        
            $appointedConfig->appointed_date = $startDate->format('Y-m-d');
            $appointedConfig->date_end = $endDate->format('Y-m-d');
        } catch (\Exception $e) {
        }

         $appointedConfig->created_by = $user->id;
         $appointedConfig->facility_id = $req->edit_facility_id;
         $appointedConfig->department_id = $req->edit_department_id;
         $appointedConfig->opdCategory = $req->SubOpdId;
         $appointedConfig->status = "config";
        //$appointedConfig->opdCategory = $req->editopd_subcateg;
         $appointedConfig->save();

        $ITConfigSched = Cofig_schedule::where('id', $req->edit_config_id)->first();

        $scheduleSlot = [];

        foreach($req->editdays as $day){
            $timeSlot = [];

            if(!empty($req->edit_time_from[$day]) && !empty($req->edit_time_to[$day])){

                foreach($req->edit_time_from[$day] as $index => $timefrom){
                    $timeto = $req->edit_time_to[$day][$index] ?? '';

                    if(!empty($timefrom) && !empty($timeto)){
                        $timeSlot[] = "{$timefrom}-{$timeto}";
                    }
                }
            }

            if(!empty($timeSlot)){
                $scheduleSlot[] = "{$day}|" . implode('|', $timeSlot);               
            }else{
                
            }
        }

        $ITConfigSched->days =  implode('|', $req->editdays);
        $ITConfigSched->time = implode('|', $scheduleSlot);
        $ITConfigSched->save();

        return redirect::back();
    }

    public function removeconfigSched(Request $req){
        $config_sched = Cofig_schedule::where('id', $req->configId)->first();
        $config_sched->delete();

        return redirect::back();
    }

    public function appointmentCalendar() {
        $user = Session::get('auth');
        $appointment_sched = AppointmentSchedule::select("appointment_schedule.*",DB::raw("sum(appointment_schedule.slot) as slot"))->groupBy('appointment_schedule.facility_id')->with('facility')->get();
        
        $facility_id = AppointmentSchedule::pluck('facility_id');

        $appointment_slot = Facility::with(['appointmentSchedules.telemedAssignedDoctor', 'appointmentSchedules.configSchedule','appointmentSchedules.subOpd'])->find($facility_id);
        
        // $config = AppointmentSchedule::with(['configSchedule' => function($query) {
        //     $query->select('id','category','days','time');
        // }])
        // ->select('configId','appointed_date','date_end')
        // ->get();
        // dd($appointment_slot);
        return view('doctor.telemedicine_calendar1',[
            'appointment_sched' => $appointment_sched,
            'appointment_slot' => $appointment_slot,
            // 'appointment_config' => $config,
            'user' => $user
        ]);
    }

    public function createAppointment(Request $request)
    {
        $user = Session::get('auth');
        
        if($request->config_id){

            $editConfig = Cofig_schedule::where('id', $request->config_id)->first();

            // $config_sched->description = $req->configdesc;
            // $config_sched->department_id = $req->department_id;
            // $config_sched->category = $req->default_category;
            // $config_sched->facility_id = $req->facility_id;
           
            $scheduleData = [];
     
            foreach($request->days as $day){
                 
                 if(isset($request->time_from[$day]) && isset($request->time_to[$day])){
     
                     $timeSlots = [];
                     foreach($request->time_from[$day] as $index => $timeFrom){
                         $timeTo = $request->time_to[$day][$index] ?? '';
                         $timeSlots[] = "{$timeFrom}-{$timeTo}";
                     }
     
                     $scheduleData[] = "{$day}|" . implode('|', $timeSlots);
                 }
            }
     
            $editConfig->days = implode('|', $request->days);
            $editConfig->time = implode('|', $scheduleData);
            $editConfig->save();


            $sched = new AppointmentSchedule();
            $sched->configId =  $request->config_id;

            try {
                $startDate = Carbon::createFromFormat('m-d-Y', $request->startDate);
                $endDate = Carbon::createFromFormat('m-d-Y', $request->endDate);
            
                $sched->appointed_date = $startDate->format('Y-m-d');
                $sched->date_end = $endDate->format('Y-m-d');
            } catch (\Exception $e) {
            }

            $sched->department_id = $request->department_id;
            $sched->facility_id = $request->facility_id;
            $sched->opdCategory = $request->subopd_id;
            $sched->created_by = $user->id;

            $sched->save();
             
            Session::put('appointment_save',true);
            return redirect()->to('manage/appointment?filterappointment=config'); 
        } else {
            for($i=1; $i<=$request->appointment_count; $i++) {

                // if (empty($request['add_appointed_time'.$i]) || empty($request['add_appointed_time_to'.$i]) || empty($request->add_opdCategory.$i) || empty($request['add_available_doctor'.$i])) {
                //     continue;
                // }
    
                $appointment_schedule = new AppointmentSchedule();
                $appointment_schedule->appointed_date = $request->appointed_date;
                $appointment_schedule->facility_id = $request->facility_id;
                $appointment_schedule->department_id = 5;
                $appointment_schedule->appointed_time = $request['add_appointed_time'.$i];
                $appointment_schedule->appointedTime_to = $request['add_appointed_time_to'.$i];
                $appointment_schedule->opdCategory = $request->subopd_id;
                // $appointment_schedule->opdCategory = $request['add_opdCategory'.$i];
                // $appointment_schedule->status = "manual";
                $appointment_schedule->slot = $request->slot;
                $appointment_schedule->created_by = $user->id;
                $appointment_schedule->save();
    
                // for($x=0; $x<count($request['add_available_doctor'.$i]); $x++) {
                //     $tele_assign_doctor = new TelemedAssignDoctor();
                //     $tele_assign_doctor->appointment_id = $appointment_schedule->id;
                //     $tele_assign_doctor->doctor_id = $request['add_available_doctor'.$i][$x];
                //     $tele_assign_doctor->created_by = $user->id;
                //     $tele_assign_doctor->save();
                // }
            }
    
            Session::put('appointment_save',true);
            return redirect()->to('manage/appointment'); 
        }

    }

    //-------------Get all booked Dates--------------------------------------
    public function getBookedDates(){
        $user = Session::get('auth');

        // $bookDates = AppointmentSchedule::where('facility_id', $user->facility_id)->pluck('appointed_date')->toArray();
        $bookDates = AppointmentSchedule::with(['telemedAssignedDoctor'])
                    ->where('facility_id', $user->facility_id)->get();
        return response()->json($bookDates);
    }
    //-------------End of get all booked dates-------------------------------
    public function getAppointmentData(Request $request)
    {
        $appointment = AppointmentSchedule::find($request->id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
        return response()->json($appointment);
    }

    //-------My version of getAppointmentData------------->

    public function displayAppointment(Request $request)
    {
        //dd($id);
        // $doctor_id =TelemedAssignDoctor::where('appointment_id', $request->id)
        // ->pluck('doctor_id')->first();
        // $appointment = AppointmentSchedule::find($request->id);
        $facility = Session::get('auth')->facility_id;

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
                'telemedAssignedDoctor' => function ($query) {
                    $query->with(['doctor' => function ($query) {
                        $query->select(
                            'id',
                            'fname',
                            'lname'
                        );
                    }]);
                }
            ])->where('facility_id', $facility)
            ->get();

           
        return response()->json($appointment);

    }

    public function deleteAppointmentSched(Request $request){
        $facility_id = Session::get('auth')->facility_id;

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
                'telemedAssignedDoctor' => function ($query) {
                    $query->with(['doctor' => function ($query) {
                        $query->select(
                            'id',
                            'fname',
                            'lname'
                        );
                    }]);
                }
            ])->where('facility_id', $facility_id)
            ->get();
    
        return response()->json($appointment);
    }

    //------------end-------------------------------------->
    // public function updateAppointment(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'id' => 'required|integer',
    //         'appointed_date' => 'required|date',
    //         'appointed_time' => 'required',
    //         'appointedTime_to' => 'required',
    //         'facility_id' => 'required',
    //         'department_id' => 'required',
    //         'opdCategory' => 'required',
    //         'slot' => 'required|integer',
    //     ]);

    //     $appointmentId = $validatedData['id'];
    //     $appointment = AppointmentSchedule::find($appointmentId);

    //     if (!$appointment) {
    //         return response()->json(['error' => 'Appointment not found'], 404);
    //     }

    //     $appointment->fill($validatedData); // Update appointment attributes

    //     if ($appointment->save()) {
    //         return response()->json(['message' => 'Appointment updated successfully'], 200);
    //     } else {
    //         return response()->json(['error' => 'Failed to update appointment'], 500);
    //     }
    // }

    //-------------------------Version of updateAppointment -----------------------------//

    public function updateAppointment(Request $request)
    {
        
        $user = Session::get('auth');
        $appointmentDate = $request->input('update_appointment_date');
        
        $appointed_id = $request->input("appointment_id");
        $appointed_date = AppointmentSchedule::where('id', $appointed_id)
            ->pluck('appointed_date')
            ->first();
      
        $requestCount = $request->appointment_count;
      
            for($i=2; $i<=$requestCount; $i++) { 
                $appointment_id = $request->input('appointment_id' . $i);
           
                $appointment = AppointmentSchedule::with(['telemedAssignedDoctor' => function ($query) {
                    $query->with(['doctor' => function ($query) {
                        $query->select('id', 'fname', 'lname');
                    }]);
                }])->find($appointment_id);
                
                if($appointment){

                    $appointment->appointed_date = $request->input('appointed_date'); 
                    $appointment->appointed_time =  $request->input('update_appointed_time' . $i);  
                    $appointment->appointedTime_to = $request->input('update_appointed_time_to' . $i);
                    $appointment->opdCategory = $request->input('opdCategory' . $i);
                    $appointment->save();
                    
                   // $appointment->telemedAssignedDoctor()->delete();

                    $doctor_ids = $request->input('Update_available_doctor' . $i);
                    if(!is_array($doctor_ids)){
                        $doctor_ids = [$doctor_ids];
                    }

                    $existingDoctorIds = $appointment->telemedAssignedDoctor->pluck('doctor_id')->toArray();
                    $doctorsToDelete = array_diff($existingDoctorIds, $doctor_ids);

                    $doctorsToAdd = array_diff($doctor_ids, $existingDoctorIds);

                    if(!empty($doctorsToDelete)){ // Delete doctors that are no longer in the request
                        $appointment->telemedAssignedDoctor()->whereIn('doctor_id', $doctorsToDelete)->delete();
                    }

                    foreach($doctorsToAdd as $doctor_id){
                        $appointment->telemedAssignedDoctor()->create([
                            'doctor_id' => $doctor_id
                        ]);
                    }
                    // foreach($appointment->telemedAssignedDoctor as $assignedoctor){
                      
                    //     $assignedoctor->doctor_id = $request->input('available_doctor' . $i);
                    //     $assignedoctor->save();
                    // }  
                }
            }
       
           
        for($i=1; $i<=$request->appointment_count; $i++) { 
                
                if(empty($request['Add_appointed_time'.$i]) || empty($request['Add_appointed_time_to'.$i]) || empty($request['opdCategory'.$i])) {
                    continue;
                }
                
                $Update_appointment_data = new AppointmentSchedule();
                $Update_appointment_data->appointed_date = $appointmentDate;
                $Update_appointment_data->facility_id = $request->facility_id;
                $Update_appointment_data->department_id = 5;
                $Update_appointment_data->appointed_time = $request['Add_appointed_time'.$i];
                $Update_appointment_data->appointedTime_to = $request['Add_appointed_time_to'.$i];
                $Update_appointment_data->opdCategory = $request['opdCategory'.$i];
                $Update_appointment_data->created_by = $user->id;
                $Update_appointment_data->save();
                
                $availableDoctors = is_array($request['Add_Update_available_doctor'.$i])
                ? $request['Add_Update_available_doctor'.$i]
                : [$request['Add_Update_available_doctor'.$i]];
    
                foreach($availableDoctors as $doctorId){
                    $tele_assign_doctor = new TelemedAssignDoctor();
                    $tele_assign_doctor->appointment_id = $Update_appointment_data->id;
                    $tele_assign_doctor->doctor_id = $doctorId;
                    $tele_assign_doctor->created_by = $user->id;
                    $tele_assign_doctor->save();
                }
        }

           
            // dd($request->all());
            Session::put('appointment_save',true);       
        
        return redirect()->back();
    }

    public function deleteTimeSlot($id){
      
        $appointedSlot = AppointmentSchedule::findOrFail($id);
        $telemedAssignDoctor = TelemedAssignDoctor::where('appointment_id', $id)->delete();
        $appointedSlot->delete();

        return response()->json(['message' => 'Appointment successfully deleted']);
    }

    //-------------------------End version of updateAppointment ------------------------//

    public function deleteAppointment(Request $request)
    {
        $appointedIds = [];
        for($i=2; $i<=$request->appointment_count; $i++) { 
            $appointedId = $request->input('appointment_id' . $i);
            $key = 'appointment_id' . $i;
            $appointed_date = AppointmentSchedule::where('id', $appointedId)
            ->pluck('appointed_date')
            ->first();
           
           $appointment = AppointmentSchedule::where('appointed_date', $appointed_date)->delete();
           //TelemedAssignDoctor

           if(array_key_exists($key, $request->all())){
                $appointedIds[] = $request->input($key);
           }
        }
    
        if(!empty($appointedIds)){

            $teleme_assign = TelemedAssignDoctor::whereIn('appointment_id', $appointedIds)->delete();
        }

        Session::put('appointment_delete',true);       
        return redirect()->back();
    
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

    public function getAvailableTimeSlots(Request $request) //appointedTimes in calendar
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
                },
                'subOpd'
            ])
            ->where('appointed_date', $request->selected_date)
            ->where('facility_id', $request->facility_id)
            ->get();

        return $timeSlots;
    }

    public function getConfigtimeSlot(Request $req){
      
        // \Log::info('Config Time Slot Request:', $req->all());
        $timeSlot = AppointmentSchedule::with(['configSchedule' => function($query) {
                $query->select('id','category','days','time');
            },
            'department' => function ($query) {
                    $query->select(
                        'id',
                        'description'
                    );
                },
            ])
            ->where('id', $req->appointedId)
            ->where('configId', $req->configId)
            ->where('facility_id', $req->facility_id)
            ->first();
            $subopd =  SubOpd::find((int) $timeSlot->opdCategory);
           
            $department_id = $timeSlot->department->description;
            $config_id = $timeSlot->configId;
            $dateStart = Carbon::parse($timeSlot->appointed_date);
            $dateEnd = Carbon::parse($timeSlot->date_end);

            $daysAndTimes = explode('|', $timeSlot->configSchedule->time);
            $timeSchedule = [];
            $currentDay = null;
            
            for ($i = 0; $i < count($daysAndTimes); $i++) {
                $item = $daysAndTimes[$i];
                
                // Check if this item is a day
                if (in_array($item, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])) {
                    $currentDay = $item;
                    if (!isset($timeSchedule[$currentDay])) {
                        $timeSchedule[$currentDay] = [];
                    }
                } 
                // If it's not a day and we have a current day, it must be a time slot
                elseif ($currentDay !== null && preg_match('/^\d{2}:\d{2}-\d{2}:\d{2}$/', $item)) {
                    $timeSchedule[$currentDay][] = $item;
                }
            }

            // Iterate through the date range and filter by selected days
            $appointedDates = [];
            $currentDate = $dateStart->copy();

            while ($currentDate->lte($dateEnd)) {
                $currentDayName = $currentDate->format('l'); // Get the day name (e.g., "Monday")

                if (array_key_exists($currentDayName, $timeSchedule)) {
                    $appointedDates[] = [
                        'appointment_id' =>$timeSlot->id,
                        'configId' => $config_id,
                        'date' => $currentDate->format('Y-m-d'),
                        'timeSlots' => $timeSchedule[$currentDayName], // Assign time slots for this day
                        'department_id' => $department_id,
                        'Opdcategory' => $subopd->description,
                        'opdSubId' =>   $subopd->id,
                    ];
                }

                $currentDate->addDay(); // Move to the next day
            }

           if($req->has('selected_date')){
                $appointedDates = array_filter($appointedDates, function($appointedDate) use ($req) {
                    return $appointedDate['date'] === $req->selected_date;
                });
           }

        return $appointedDates;
    }

    public function countconsultation(){

        $countSubOpd = SubOpd::distinct('id')->count('id');
       
        return view('doctor.reportConsultation', [
            'countDepartment' => $countSubOpd ,
        ]);
    }

    public function getconfigAppointment(Request $req){

        $selectedTime = $req->selectedTime;

        $startTimes = collect($selectedTime)->map(function ($timeSlot){
            return date('H:i:s', strtotime(substr($timeSlot, 0, 5)));
        });
        
       $slotUsed = TelemedAssignDoctor::where('appointment_id', $req->appointmentId)
                        ->where('appointed_date', $req->date)
                        ->get();
                        
        return response()->json($slotUsed);
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

