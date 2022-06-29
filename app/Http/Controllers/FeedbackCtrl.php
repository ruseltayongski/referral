<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Feedback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FeedbackCtrl extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function home() {
        return view('feedback.feedback');
    }

    public function CommentAppend(Request $request) {
        $textarea_body = view('feedback.textarea_body');
        $comment_append = view('feedback.comment_append',[
            "comment" => $request->comment
        ]);
        $view = $textarea_body.'explode|ruseltayong|explode'.$comment_append;
        return $view;
    }

    public function recoView() {
        return view('reco.reco');
    }

    public function recoFetch(Request $request) {
        $user = Session::get('auth');
        $start = Carbon::now()->startOfYear()->format('m/d/Y');
        $end = Carbon::now()->endOfDay()->format('m/d/Y');

        $data = Activity::select(
            'feedback.*',
            \DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            \DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            \DB::raw('COALESCE(CONCAT(users.fname," ",users.mname," ",users.lname),"WALK IN") as referring_md'),
            'patients.sex',
            'patients.id as patient_id',
            'patients.contact',
            'users.level as user_level'
        )
            ->leftJoin('patients','patients.id','=','activity.patient_id')
            ->leftJoin('tracking','tracking.code','=','activity.code')
            ->leftJoin('users','users.id','=',\DB::raw("if(activity.referring_md,activity.referring_md,activity.action_md)"))
            ->join("feedback","feedback.code","=","activity.code")
            ->leftJoin('feedback as fac2',function($join){
                $join->on("feedback.code","=","fac2.code");
                $join->on("feedback.id","<","fac2.id");
            })
            ->whereNull("fac2.id")
            ->where(function($query) use ($user) {
                $query->where("activity.referred_from",$user->facility_id)
                        ->orWhere("activity.referred_to",$user->facility_id);
            });

        $start_date = Carbon::parse($start)->startOfDay();
        $end_date = Carbon::parse($end)->endOfDay();

        $data = $data->whereBetween('activity.created_at',[$start_date,$end_date])
            ->where(function($q){
                $q->where('activity.status','referred')
                    ->orwhere('activity.status','redirected')
                    ->orwhere('activity.status','transferred');
            })
            ->groupBy("activity.code")
            ->orderBy("activity.created_at","desc")
            ->get();

        return $data;
    }

    public function recoSelect($code) {
        $facility_id = Session::get('auth')->facility_id;
        $data = Activity::select(
            'feedback.id',
            'feedback.message',
            \DB::raw("DATE_FORMAT(feedback.created_at, '%d %b %l:%i %p') as send_date"),
            \DB::raw("if(users.facility_id = $facility_id, 'right','left') as position"),
            \DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            \DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            'patients.sex',
            'patients.id as patient_id',
            'patients.contact',
            'facility.name as facility_name',
            \DB::raw('CONCAT(
                if(users.level="doctor","Dr. ",""),
            users.fname," ",users.mname," ",users.lname) as sender_name')
        )
            ->leftJoin('patients','patients.id','=','activity.patient_id')
            ->leftJoin('tracking','tracking.code','=','activity.code')
            ->leftJoin("feedback","feedback.code","=","activity.code")
            ->leftJoin('users','users.id','=','feedback.sender')
            ->leftJoin('facility','facility.id','=','users.facility_id')
            ->where("activity.code",$code)
            ->groupBy("feedback.id")
            ->get();

        return $data;
    }
}
