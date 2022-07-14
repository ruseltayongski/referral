<?php

namespace App\Http\Controllers\admin;

use App\Appointment;
use App\UserFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ApptCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function appointment(Request $req) {
        if($req->view_all) {
            $keyword = '';
        } else {
            $keyword = $req->appt_keyword;
        }
        $data = Appointment::orderBy('id', 'desc');

        if($keyword && $keyword != '') {
            $data = $data->where('name', 'like', '%'.$keyword.'%');
        }

        $status = $req->status_filter;
        if($status && $status != '') {
            $data = $data->where('status', $status);
        }

        if($req->date_filter) {
            $data = $data->where('preferred_date',$req->date_filter);
        }

        $data = $data->paginate(25);

        return view('admin.appointment.appt',[
            'title' => 'Appointment',
            'data' => $data
        ]);
    }

    public function appointmentDetails(Request $req) {
        $data = Appointment::where('id', $req->id)->first();
        if($data->status == 'new') {
            $data->status = 'seen';
            $data->save();
        }
        return $data;
    }

    public function approveAppointment(Request $req) {
        $data = Appointment::where('id', $req->id)->first();
        $data->preferred_date = $req->date;
        $data->status = 'approved';
        $data->save();
        Session::put('appt_msg','Successfully approved appointment!');
        Session::put('appt_notif',true);
        return Redirect::back();
    }

    public function feedback(Request $req){
        $keyword = $req->keyword;
        if($req->view_all) {
            $keyword = '';
        }
        $data = UserFeedback::orderBy('id', 'desc');

        if($keyword && $keyword != '') {
            $data = $data->where('name', 'like', '%'.$keyword.'%');
        }

        $data = $data->paginate(25);

        return view('admin.user_feedback.user_feedback',[
            'title' => 'User Feedback',
            'data' => $data
        ]);
    }
}