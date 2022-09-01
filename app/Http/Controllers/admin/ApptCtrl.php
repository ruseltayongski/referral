<?php

namespace App\Http\Controllers\admin;

use App\Appointment;
use App\Feedback;
use App\UserFeedback;
use Carbon\Carbon;
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
            Session::put('appt_keyword','');
            Session::put('appt_date','');
        } else {
            $keyword = $req->appt_keyword;
            if(!$keyword || $keyword == '') {
                $keyword = Session::get('appt_keyword');
            }
            $date = $req->date_filter;
            if(!$date) {
                $date = Session::get('appt_date');
            }
        }

        Session::put('appt_keyword', $keyword);
        Session::put('appt_date',$date);

        $data = Appointment::orderBy('id', 'desc');

        if($keyword && $keyword != '') {
            $data = $data->where('name', 'like', '%'.$keyword.'%');
        }

        $status = $req->status_filter;
        if($status && $status != '') {
            $data = $data->where('status', $status);
        }

        if($date) {
            $start = Carbon::parse($date)->startOfDay();
            $end = Carbon::parse($date)->endOfDay();
            $data = $data
                ->whereBetween('created_at', [$start, $end]);
        }
        $count = $data->count();

        $data = $data->paginate(30);

        return view('admin.appointment.appt',[
            'title' => 'Appointment',
            'data' => $data,
            'count' => $count
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

    public function apptResolve(Request $req) {
        $data = Appointment::where('id', $req->id)->first();
        $data->remarks = $req->remarks;
        $data->status = 'resolved';
        $data->save();
        Session::put('appt_msg','Successfully resolved appointment!');
        Session::put('appt_notif',true);
        return Redirect::back();
    }

    public function feedback(Request $req){
        if($req->view_all) {
            $keyword = '';
        } else {
            $keyword = $req->keyword;
            if(!$keyword || $keyword == '') {
                $keyword = Session::get('user_feedback_keyword');
            }
        }

        Session::put('user_feedback_keyword', $keyword);

        $data = UserFeedback::orderBy('id', 'desc');

        if($keyword && $keyword != '') {
            $data = $data->where('name', 'like', '%'.$keyword.'%');
        }

        $count = $data->count();
        $data = $data->paginate(30);

        return view('admin.user_feedback.user_feedback',[
            'title' => 'User Feedback',
            'data' => $data,
            'count' => $count
        ]);
    }

    public function feedbackSeen(Request $req) {
        $feedback = UserFeedback::where('id', $req->id)->first();
        $feedback->status = 'seen';
        $feedback->save();
        return array('success' => true);
    }

    public function feedbackDetails(Request $req) {
        return UserFeedback::where('id', $req->id)->first();
    }

    public function feedbackResolve(Request $req) {
        $feedback = UserFeedback::where('id', $req->id)->first();
        $feedback->remarks = $req->remarks;
        $feedback->status = 'resolved';
        $feedback->save();
        Session::put('user_feedback_msg','User Feedback resolved!');
        Session::put('user_feedback_notif',true);
        return Redirect::back();
    }

}