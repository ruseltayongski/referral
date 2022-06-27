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
        /*$search = $request->search;
        $option_filter = $request->option_filter;*/
        //$date = $request->date_range;
        /*$facility_filter = $request->facility_filter;
        $department_filter = $request->department_filter;*/

        $start = Carbon::now()->startOfYear()->format('m/d/Y');
        $end = Carbon::now()->endOfDay()->format('m/d/Y');

        $data = Activity::select(
            'feedback.*',
            \DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            \DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            \DB::raw('COALESCE(CONCAT(users.fname," ",users.mname," ",users.lname),"WALK IN") as referring_md'),
            'patients.sex',
            'facility.name as facility_name',
            'facility.id as facility_id',
            'patients.id as patient_id',
            'patients.contact',
            'users.level as user_level',
            'activity.referring_md as wew'
        )
            ->leftJoin('patients','patients.id','=','activity.patient_id')
            ->leftJoin('facility','facility.id','=','activity.referred_to')
            ->leftJoin('tracking','tracking.code','=','activity.code')
            ->leftJoin('users','users.id','=',\DB::raw("if(activity.referring_md,activity.referring_md,activity.action_md)"))
            ->join("feedback","feedback.code","=","activity.code")
            ->where(function($query) use ($user) {
                $query->where("activity.referred_from",$user->facility_id)
                        ->orWhere("activity.referred_to",$user->facility_id);
            });

        /*if($search){
            $data = $data->where(function($q) use ($search){
                $q->where('patients.fname','like',"%$search%")
                    ->orwhere('patients.mname','like',"%$search%")
                    ->orwhere('patients.lname','like',"%$search%")
                    ->orwhere('activity.code','like',"%$search%");
            });
        }*/

        /*if($option_filter)
            $data = $data->where('activity.status',$option_filter);
        if($facility_filter)
            $data = $data->where('activity.referred_to',$facility_filter);
        if($department_filter)
            $data = $data->where('activity.department_id',$department_filter);*/

        /*if($date) {
            $range = explode('-',str_replace(' ', '', $date));
            $start = $range[0];
            $end = $range[1];
        }*/

        $start_date = Carbon::parse($start)->startOfDay();
        $end_date = Carbon::parse($end)->endOfDay();

        $data = $data->whereBetween('activity.created_at',[$start_date,$end_date])
            ->where(function($q){
                $q->where('activity.status','referred')
                    ->orwhere('activity.status','redirected')
                    ->orwhere('activity.status','transferred');
            })
            ->orderBy('activity.id','desc')
            ->groupBy("activity.code")
            ->get();


        return $data;
    }

    public function recoSelect($code) {
        $facility_id = Session::get('auth')->facility_id;
        $data = Activity::select(
            'feedback.id',
            'feedback.message',
            \DB::raw("if(users.facility_id = $facility_id, 'me', 'you') as position"),
            \DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as patient_name'),
            \DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS age"),
            'patients.sex',
            'patients.id as patient_id',
            'patients.contact',
            'activity.referring_md as wew'
        )
            ->leftJoin('patients','patients.id','=','activity.patient_id')
            ->leftJoin('tracking','tracking.code','=','activity.code')
            ->leftJoin("feedback","feedback.code","=","activity.code")
            ->leftJoin('users','users.id','=','feedback.sender')
            ->where("activity.code",$code)
            ->groupBy("feedback.id")
            ->orderBy('activity.id','desc')
            ->get();

        return $data;
    }
}
