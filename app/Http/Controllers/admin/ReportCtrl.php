<?php

namespace App\Http\Controllers\admin;

use App\Activity;
use App\Facility;
use App\Http\Controllers\ApiController;
use App\Icd;
use App\Icd10;
use App\Login;
use App\PatientForm;
use App\PregnantForm;
use App\Province;
use App\Seen;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class ReportCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('admin');
        //$this->middleware('doctor');
    }

    public function online()
    {
        $date = Session::get('dateReportOnline');
        if(!$date){
            $date = date('Y-m-d');
        }

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $data = Login::select(
                    'login.*',
                        'users.*',
                        'login.status as login_status'
                )
                ->join('users','users.id','=','login.userId')
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->where('users.level','!=','admin')
                ->where('users.username','!=','rtayong_doctor')
                ->orderBy('users.facility_id','asc')
                ->orderBy('login.id','desc')
                ->orderBy('users.lname','asc')
                ->paginate(20);

        return view('admin.online',[
            'title' => 'Online Users',
            'data' => $data
        ]);
    }

    public function filterOnline(Request $req)
    {
        Session::put('dateReportOnline',$req->date);
        return self::online();
    }

    public function online1() //12/23/2019 created
    {
        $date = Session::get('dateReportOnline');
        if(!$date)
            $date = date('Y-m-d');

        $start = date('Y-m-d',strtotime($date)).' 00:00:00';
        $end = date('Y-m-d',strtotime($date)).' 23:59:59';

        $data = \DB::connection('mysql')->select("call AttendanceFunc('$start','$end')");

        return view('admin.online',[
            'title' => 'Online Users',
            'data' => $data
        ]);
    }

    public function filterOnline1(Request $req)
    {
        Session::put('dateReportOnline',$req->date);
        return self::online1();
    }

    public function referral()
    {
        $start = Session::get('startDateReportReferral');
        $end = Session::get('endDateReportReferral');
        if(!$start)
            $start = date('Y-m-d');
        if(!$end)
            $end = date('Y-m-d');

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

        $data = Tracking::whereBetween('updated_at',[$start,$end])
            ->orderBy('updated_at','desc')
            ->paginate(20);
        return view('admin.referral',[
            'title' => 'Referral Status',
            'data' => $data
        ]);
    }

    public function filterReferral(Request $req)
    {
        $range = explode('-',str_replace(' ', '', $req->date));
        $tmp1 = explode('/',$range[0]);
        $tmp2 = explode('/',$range[1]);

        $start = $tmp1[2].'-'.$tmp1[0].'-'.$tmp1[1];
        $end = $tmp2[2].'-'.$tmp2[0].'-'.$tmp2[1];

        Session::put('startDateReportReferral',$start);
        Session::put('endDateReportReferral',$end);
        return self::referral();
    }

    public function graph(){
        return view('admin.report.graph');
    }

    public function bar_chart(){
        return view('admin.report.bar_chart');
    }

    public function createDateRangeArray($strDateFrom,$strDateTo)
    {
        $aryRange = [];

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        $iDateFrom -= 86400;
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }

        return $aryRange;
    }

    public function formatTheTAT($mins) {
        $hours = floor($mins /60);
        $mins  = $mins %60;

        if((int)$hours > 24){
            $days = floor($hours /24);
            $hours = $hours %24;
        }
        if(isset($days))
            $days .= $days > 1 ? " Days " : " Day ";

        if($hours > 0)
            $hours .= $hours > 1 ? " Hours " : " Hour ";
        else
            $hours = "";

        $mins .= $mins > 1 ? " Minutes " : " Minute ";

        return $days.$hours.$mins;
    }

    public function turnAroundTime(Request $request) {
        $user = Session::get('auth');
        if(isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0]));
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1]));
        } else {
            $date_start = date('Y-m-d',strtotime(Carbon::now()->subDays(15)));
            $date_end = date('Y-m-d');
        }

        $iDateFrom = mktime(1, 0, 0, substr($date_start, 5, 2), substr($date_start, 8, 2), substr($date_start, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($date_end, 5, 2), substr($date_end, 8, 2), substr($date_end, 0, 4));

        $iDateFrom -= 86400;
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            $per_day = date("Y-m-d",$iDateFrom);

            $referred = Activity::
            select("activity.code","activity.created_at")
                ->where("activity.referred_to",$user->facility_id)
                ->whereBetween("activity.created_at",[$per_day." 00:00:00",$per_day." 23:59:59"])
                ->where('activity.status','referred')
                ->get();

            $redirected = Activity::
            select("activity.code","activity.created_at")
                ->where("activity.referred_to",$user->facility_id)
                ->whereBetween("activity.created_at",[$per_day." 00:00:00",$per_day." 23:59:59"])
                ->where('activity.status','redirected')
                ->get();

            $transferred = Activity::
            select("activity.code","activity.created_at")
                ->where("activity.referred_to",$user->facility_id)
                ->whereBetween("activity.created_at",[$per_day." 00:00:00",$per_day." 23:59:59"])
                ->where('activity.status','transferred')
                ->get();

            foreach($referred as $refer) {
                $refer_to_seen = Seen::where("code",$refer->code)
                    ->where("created_at",">=",$refer->created_at)
                    ->where("facility_id",$user->facility_id)
                    ->first();

                if($refer_to_seen) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_seen->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $refer_seen_holder[] = $diff_in_minutes; //refer to seen

                    $seen_to_accept = Activity::where("code",$refer_to_seen->code)
                        ->where("created_at",">=",$refer_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","accepted")
                        ->first();
                    if($seen_to_accept) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_accept_holder[] = $diff_in_minutes; // seen to accept

                        $accept_to_arrive = Activity::where("code",$seen_to_accept->code)
                            ->where("created_at",">=",$seen_to_accept->created_at)
                            ->where("referred_from",$user->facility_id)
                            ->where("status","arrived")
                            ->first();
                        if($accept_to_arrive) {
                            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept->created_at);
                            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive->created_at);
                            $diff_in_minutes = $to->diffInMinutes($from);

                            $accept_arrive_holder[] = $diff_in_minutes; // accept to arrive

                            $arrive_to_admit = Activity::where("code",$accept_to_arrive->code)
                                ->where("created_at",">=",$accept_to_arrive->created_at)
                                ->where("referred_from",$user->facility_id)
                                ->where("status","admitted")
                                ->first();

                            if($arrive_to_admit) {
                                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive->created_at);
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit->created_at);
                                $diff_in_minutes = $to->diffInMinutes($from);

                                $arrive_admit_holder[] = $diff_in_minutes; // arrive to admit

                                $admit_to_discharge = Activity::where("code",$arrive_to_admit->code)
                                    ->where("created_at",">=",$arrive_to_admit->created_at)
                                    ->where("referred_from",$user->facility_id)
                                    ->where("status","discharged")
                                    ->first();
                                if($admit_to_discharge) {
                                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit->created_at);
                                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge->created_at);
                                    $diff_in_minutes = $to->diffInMinutes($from);

                                    $admit_discharge_holder[] = $diff_in_minutes; // admit to discharge
                                }
                                else {
                                    $admit_to_discharge = Activity::where("code","220409-063-113421631859")
                                        ->where("created_at",">=",$accept_to_arrive->created_at)
                                        ->where("referred_from",$user->facility_id)
                                        ->where("status","discharged")
                                        ->first();
                                    if($admit_to_discharge) {
                                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive->created_at);
                                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge->created_at);
                                        $diff_in_minutes = $to->diffInMinutes($from);

                                        $admit_discharge_holder[] = $diff_in_minutes; // admit to discharge
                                    }
                                }
                            }
                        }
                    }

                    $seen_to_reject = Activity::where("code",$refer_to_seen->code)
                        ->where("created_at",">=",$refer_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","rejected")
                        ->first();
                    if($seen_to_reject) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_reject->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_reject_holder[] = $diff_in_minutes; // seen to rejected
                    }
                }

                $refer_to_accept = Activity::where("code",$refer->code)
                    ->where("created_at",">=",$refer->created_at)
                    ->where("referred_to",$user->facility_id)
                    ->where("status","accepted")
                    ->first();

                if($refer_to_accept) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_accept->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $refer_accept_holder[] = $diff_in_minutes;
                }
            }

            foreach($redirected as $redirect) {
                $redirect_to_seen = Seen::where("code",$redirect->code)
                    ->where("created_at",">=",$redirect->created_at)
                    ->where("facility_id",$user->facility_id)
                    ->first();

                if($redirect_to_seen) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_seen->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $redirect_seen_holder[] = $diff_in_minutes; //redirect to seen

                    $seen_to_accept_redirect = Activity::where("code",$redirect_to_seen->code)
                        ->where("created_at",">=",$redirect_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","accepted")
                        ->first();
                    if($seen_to_accept_redirect) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept_redirect->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_accept_holder_redirect[] = $diff_in_minutes; // seen to accept

                        $accept_to_arrive_redirect = Activity::where("code",$seen_to_accept_redirect->code)
                            ->where("created_at",">=",$seen_to_accept_redirect->created_at)
                            ->where("referred_from",$user->facility_id)
                            ->where("status","arrived")
                            ->first();
                        if($accept_to_arrive_redirect) {
                            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept_redirect->created_at);
                            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive_redirect->created_at);
                            $diff_in_minutes = $to->diffInMinutes($from);

                            $accept_arrive_holder_redirect[] = $diff_in_minutes; // accept to arrive

                            $arrive_to_admit_redirect = Activity::where("code",$accept_to_arrive_redirect->code)
                                ->where("created_at",">=",$accept_to_arrive_redirect->created_at)
                                ->where("referred_from",$user->facility_id)
                                ->where("status","admitted")
                                ->first();

                            if($arrive_to_admit_redirect) {
                                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive_redirect->created_at);
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit_redirect->created_at);
                                $diff_in_minutes = $to->diffInMinutes($from);

                                $arrive_admit_holder_redirect[] = $diff_in_minutes; // arrive to admit

                                $admit_to_discharge_redirect = Activity::where("code",$arrive_to_admit_redirect->code)
                                    ->where("created_at",">=",$arrive_to_admit_redirect->created_at)
                                    ->where("referred_from",$user->facility_id)
                                    ->where("status","discharged")
                                    ->first();
                                if($admit_to_discharge_redirect) {
                                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit_redirect->created_at);
                                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge_redirect->created_at);
                                    $diff_in_minutes = $to->diffInMinutes($from);

                                    $admit_discharge_holder_redirect[] = $diff_in_minutes; // admit to discharge
                                }
                                else {
                                    $admit_to_discharge_redirect = Activity::where("code","220409-063-113421631859")
                                        ->where("created_at",">=",$accept_to_arrive_redirect->created_at)
                                        ->where("referred_from",$user->facility_id)
                                        ->where("status","discharged")
                                        ->first();
                                    if($admit_to_discharge_redirect) {
                                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive_redirect->created_at);
                                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge_redirect->created_at);
                                        $diff_in_minutes = $to->diffInMinutes($from);

                                        $admit_discharge_holder_redirect[] = $diff_in_minutes; // admit to discharge
                                    }
                                }
                            }
                        }
                    }

                    $seen_to_reject_redirect = Activity::where("code",$redirect_to_seen->code)
                        ->where("created_at",">=",$redirect_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","rejected")
                        ->first();
                    if($seen_to_reject_redirect) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_reject_redirect->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_reject_holder_redirect[] = $diff_in_minutes; // seen to rejected
                    }
                }


                $redirect_to_accept = Activity::where("code",$redirect->code)
                    ->where("created_at",">=",$redirect->created_at)
                    ->where("referred_to",$user->facility_id)
                    ->where("status","accepted")
                    ->first();

                if($redirect_to_accept) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_accept->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $redirect_accept_holder[] = $diff_in_minutes;
                }
            }

            foreach($transferred as $transfer) {
                $transfer_to_accept = Activity::where("code",$transfer->code)
                    ->where("created_at",">=",$transfer->created_at)
                    ->where("referred_to",$user->facility_id)
                    ->where("status","accepted")
                    ->first();

                if($transfer_to_accept) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $transfer->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $transfer_to_accept->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $transfer_accept_holder[] = $diff_in_minutes;
                }
            }

            $data[] = [
                "date" => date("M d",strtotime($per_day)),
                "referred" => count($referred),
                "redirected" => count($redirected),
                "transferred" => count($transferred),
                "refer_to_accept" => round(collect($refer_accept_holder)->avg(),2),
                "redirect_to_accept" => round(collect($redirect_accept_holder)->avg(),2),
                "transfer_to_accept" => round(collect($transfer_accept_holder)->avg(),2)
            ];
        }

        /*for($day = 1; $day <= 15; $day++) {
            $referred = Activity::
                select("activity.code","activity.created_at")
                ->where("activity.referred_to",$user->facility_id)
                ->whereBetween("activity.created_at",["2022-04-$day 00:00:00","2022-04-$day 23:59:59"])
                ->where('activity.status','referred')
                ->get();

            $redirected = Activity::
                select("activity.code","activity.created_at")
                ->where("activity.referred_to",$user->facility_id)
                ->whereBetween("activity.created_at",["2022-04-$day 00:00:00","2022-04-$day 23:59:59"])
                ->where('activity.status','redirected')
                ->get();

            $transferred = Activity::
                select("activity.code","activity.created_at")
                ->where("activity.referred_to",$user->facility_id)
                ->whereBetween("activity.created_at",["2022-04-$day 00:00:00","2022-04-$day 23:59:59"])
                ->where('activity.status','transferred')
                ->get();

            foreach($referred as $refer) {
                $refer_to_seen = Seen::where("code",$refer->code)
                        ->where("created_at",">=",$refer->created_at)
                        ->where("facility_id",$user->facility_id)
                        ->first();

                if($refer_to_seen) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_seen->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $refer_seen_holder[] = $diff_in_minutes; //refer to seen


                    $seen_to_accept = Activity::where("code",$refer_to_seen->code)
                        ->where("created_at",">=",$refer_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","accepted")
                        ->first();
                    if($seen_to_accept) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_accept_holder[] = $diff_in_minutes; // seen to accept

                        $accept_to_arrive = Activity::where("code",$seen_to_accept->code)
                            ->where("created_at",">=",$seen_to_accept->created_at)
                            ->where("referred_from",$user->facility_id)
                            ->where("status","arrived")
                            ->first();
                        if($accept_to_arrive) {
                            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept->created_at);
                            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive->created_at);
                            $diff_in_minutes = $to->diffInMinutes($from);

                            $accept_arrive_holder[] = $diff_in_minutes; // accept to arrive

                            $arrive_to_admit = Activity::where("code",$accept_to_arrive->code)
                                ->where("created_at",">=",$accept_to_arrive->created_at)
                                ->where("referred_from",$user->facility_id)
                                ->where("status","admitted")
                                ->first();

                            if($arrive_to_admit) {
                                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive->created_at);
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit->created_at);
                                $diff_in_minutes = $to->diffInMinutes($from);

                                $arrive_admit_holder[] = $diff_in_minutes; // arrive to admit

                                $admit_to_discharge = Activity::where("code",$arrive_to_admit->code)
                                    ->where("created_at",">=",$arrive_to_admit->created_at)
                                    ->where("referred_from",$user->facility_id)
                                    ->where("status","discharged")
                                    ->first();
                                if($admit_to_discharge) {
                                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit->created_at);
                                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge->created_at);
                                    $diff_in_minutes = $to->diffInMinutes($from);

                                    $admit_discharge_holder[] = $diff_in_minutes; // admit to discharge
                                }
                                else {
                                    $admit_to_discharge = Activity::where("code","220409-063-113421631859")
                                        ->where("created_at",">=",$accept_to_arrive->created_at)
                                        ->where("referred_from",$user->facility_id)
                                        ->where("status","discharged")
                                        ->first();
                                    if($admit_to_discharge) {
                                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive->created_at);
                                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge->created_at);
                                        $diff_in_minutes = $to->diffInMinutes($from);

                                        $admit_discharge_holder[] = $diff_in_minutes; // admit to discharge
                                    }
                                }
                            }
                        }
                    }

                    $seen_to_reject = Activity::where("code",$refer_to_seen->code)
                        ->where("created_at",">=",$refer_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","rejected")
                        ->first();
                    if($seen_to_reject) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_reject->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_reject_holder[] = $diff_in_minutes; // seen to rejected
                    }
                }

                $refer_to_accept = Activity::where("code",$refer->code)
                    ->where("created_at",">=",$refer->created_at)
                    ->where("referred_to",$user->facility_id)
                    ->where("status","accepted")
                    ->first();

                if($refer_to_accept) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $refer_to_accept->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $refer_accept_holder[] = $diff_in_minutes;
                }
            }

            foreach($redirected as $redirect) {
                $redirect_to_seen = Seen::where("code",$redirect->code)
                    ->where("created_at",">=",$redirect->created_at)
                    ->where("facility_id",$user->facility_id)
                    ->first();

                if($redirect_to_seen) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_seen->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $redirect_seen_holder[] = $diff_in_minutes; //redirect to seen

                    $seen_to_accept_redirect = Activity::where("code",$redirect_to_seen->code)
                        ->where("created_at",">=",$redirect_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","accepted")
                        ->first();
                    if($seen_to_accept_redirect) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept_redirect->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_accept_holder_redirect[] = $diff_in_minutes; // seen to accept

                        $accept_to_arrive_redirect = Activity::where("code",$seen_to_accept_redirect->code)
                            ->where("created_at",">=",$seen_to_accept_redirect->created_at)
                            ->where("referred_from",$user->facility_id)
                            ->where("status","arrived")
                            ->first();
                        if($accept_to_arrive_redirect) {
                            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_accept_redirect->created_at);
                            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive_redirect->created_at);
                            $diff_in_minutes = $to->diffInMinutes($from);

                            $accept_arrive_holder_redirect[] = $diff_in_minutes; // accept to arrive

                            $arrive_to_admit_redirect = Activity::where("code",$accept_to_arrive_redirect->code)
                                ->where("created_at",">=",$accept_to_arrive_redirect->created_at)
                                ->where("referred_from",$user->facility_id)
                                ->where("status","admitted")
                                ->first();

                            if($arrive_to_admit_redirect) {
                                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive_redirect->created_at);
                                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit_redirect->created_at);
                                $diff_in_minutes = $to->diffInMinutes($from);

                                $arrive_admit_holder_redirect[] = $diff_in_minutes; // arrive to admit

                                $admit_to_discharge_redirect = Activity::where("code",$arrive_to_admit_redirect->code)
                                    ->where("created_at",">=",$arrive_to_admit_redirect->created_at)
                                    ->where("referred_from",$user->facility_id)
                                    ->where("status","discharged")
                                    ->first();
                                if($admit_to_discharge_redirect) {
                                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $arrive_to_admit_redirect->created_at);
                                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge_redirect->created_at);
                                    $diff_in_minutes = $to->diffInMinutes($from);

                                    $admit_discharge_holder_redirect[] = $diff_in_minutes; // admit to discharge
                                }
                                else {
                                    $admit_to_discharge_redirect = Activity::where("code","220409-063-113421631859")
                                        ->where("created_at",">=",$accept_to_arrive_redirect->created_at)
                                        ->where("referred_from",$user->facility_id)
                                        ->where("status","discharged")
                                        ->first();
                                    if($admit_to_discharge_redirect) {
                                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $accept_to_arrive_redirect->created_at);
                                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $admit_to_discharge_redirect->created_at);
                                        $diff_in_minutes = $to->diffInMinutes($from);

                                        $admit_discharge_holder_redirect[] = $diff_in_minutes; // admit to discharge
                                    }
                                }
                            }
                        }
                    }

                    $seen_to_reject_redirect = Activity::where("code",$redirect_to_seen->code)
                        ->where("created_at",">=",$redirect_to_seen->created_at)
                        ->where("referred_to",$user->facility_id)
                        ->where("status","rejected")
                        ->first();
                    if($seen_to_reject_redirect) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_seen->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $seen_to_reject_redirect->created_at);
                        $diff_in_minutes = $to->diffInMinutes($from);

                        $seen_reject_holder_redirect[] = $diff_in_minutes; // seen to rejected
                    }
                }


                $redirect_to_accept = Activity::where("code",$redirect->code)
                    ->where("created_at",">=",$redirect->created_at)
                    ->where("referred_to",$user->facility_id)
                    ->where("status","accepted")
                    ->first();

                if($redirect_to_accept) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $redirect_to_accept->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $redirect_accept_holder[] = $diff_in_minutes;
                }
            }

            foreach($transferred as $transfer) {
                $transfer_to_accept = Activity::where("code",$transfer->code)
                    ->where("created_at",">=",$transfer->created_at)
                    ->where("referred_to",$user->facility_id)
                    ->where("status","accepted")
                    ->first();

                if($transfer_to_accept) {
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $transfer->created_at);
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $transfer_to_accept->created_at);
                    $diff_in_minutes = $to->diffInMinutes($from);

                    $transfer_accept_holder[] = $diff_in_minutes;
                }
            }

            $data[] = [
                "date" => "April ".$day,
                "referred" => count($referred),
                "redirected" => count($redirected),
                "transferred" => count($transferred),
                "refer_to_accept" => round(collect($refer_accept_holder)->avg(),2),
                "redirect_to_accept" => round(collect($redirect_accept_holder)->avg(),2),
                "transfer_to_accept" => round(collect($transfer_accept_holder)->avg(),2)
            ];
        }*/

        return view('admin.report.tat',[
            "data_points" => $data,

            "refer_to_seen" => $this->formatTheTAT(round(collect($refer_seen_holder)->avg(),2)),
            "seen_to_accept" => $this->formatTheTAT(round(collect($seen_accept_holder)->avg(),2)),
            "seen_to_reject" => $this->formatTheTAT(round(collect($seen_reject_holder)->avg(),2)),
            "accept_to_arrive" => $this->formatTheTAT(round(collect($accept_arrive_holder)->avg(),2)),
            "arrive_to_admit" => $this->formatTheTAT(round(collect($arrive_admit_holder)->avg(),2)),
            "admit_to_discharge" => $this->formatTheTAT(round(collect($admit_discharge_holder)->avg(),2)),


            "redirect_to_seen" => $this->formatTheTAT(round(collect($redirect_seen_holder)->avg(),2)),
            "seen_to_accept_redirect" => $this->formatTheTAT(round(collect($seen_accept_holder_redirect)->avg(),2)),
            "seen_to_reject_redirect" => $this->formatTheTAT(round(collect($seen_reject_holder_redirect)->avg(),2)),
            "accept_to_arrive_redirect" => $this->formatTheTAT(round(collect($accept_arrive_holder_redirect)->avg(),2)),
            "arrive_to_admit_redirect" => $this->formatTheTAT(round(collect($arrive_admit_holder_redirect)->avg(),2)),
            "admit_to_discharge_redirect" => $this->formatTheTAT(round(collect($admit_discharge_holder_redirect)->avg(),2)),

            "date_start" => $date_start,
            "date_end" => $date_end
        ]);
    }

    public function onlineFacility(Request $request){
        if($request->isMethod('post') && isset($request->day_date)){
            $day_date = date('Y-m-d',strtotime($request->day_date));
        } else {
            $day_date = date('Y-m-d');
        }
        $stored_name = "online_facility('$day_date')";
        $data = \DB::connection('mysql')->select("call $stored_name");

        return view('admin.report.online_facility',[
            'title' => 'ONLINE FACILITY',
            "data" => $data,
            'day_date' => $day_date
        ]);
    }

    public function offlineFacility(Request $request,$province_id){
        if($request->isMethod('post') && isset($request->day_date))
            $day_date = date('Y-m-d',strtotime($request->day_date));
        else
            $day_date = date('Y-m-d');

        $data = \DB::connection('mysql')->select("call offline_facility('$day_date','$province_id')");

        return view('admin.report.offline_facility',[
            'title' => 'Offline Facility',
            "data" => $data,
            'day_date' => $day_date,
            'province_id' => $province_id
        ]);
    }

    public function weeklyReport($province_id,Request $request){
        if($request->isMethod('post') && isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0]));
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1]));
        } else {
            $date_start = date('Y-m-d',strtotime(Carbon::now()->subDays(31)));
            $date_end = date('Y-m-d');
        }

        $facility = Facility::select("id as facility_id","hospital_type","name")->where("referral_used","yes")
            ->where("province",$province_id)
            ->orderBy(DB::raw("
                CASE
                    WHEN hospital_type = 'government' THEN 'a'
                    WHEN hospital_type = 'private' THEN 'b'
                    ELSE hospital_type
                END 
            "),"asc")
            ->orderBy("name","desc")
            ->get();
        $generate_weeks = \DB::connection('mysql')->select("call generate_weeks('$date_start','$date_end')");

        return view('admin.report.offline_facility_weekly',[
            'title' => 'Login Status',
            'facility' => $facility,
            'generate_weeks' => $generate_weeks,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "province" => Province::find($province_id)->description,
            "province_id" => $province_id
        ]);
    }

    public function onboardFacility(Request $request,$province_id){
        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Activity::select("created_at")->orderBy("created_at","asc")->first()->created_at;
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $data = \DB::connection('mysql')->select("call onboard_facility('$province_id','$date_start','$date_end')");

        return view('admin.report.onboard_facility',[
            'title' => 'ONBOARD FACILITY',
            'data' => $data,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'province_id' => $province_id
        ]);
    }

    public function onboardUsers(){
        $onboard_users = \DB::connection('mysql')->select("call onboard_users()");
        return view('admin.report.onboard_users',[
            "onboard_users" => $onboard_users
        ]);
    }

    public function statisticsReport(Request $request,$province){
        if(isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
            $date_end = Carbon::now()->endOfDay()->format('Y-m-d').' 23:59:59';
            $request->date_range = $date_start." - ".$date_end;
        }
        $apiCtrl = new ApiController();
        $request->province = $province;
        $data = $apiCtrl->api($request);
        return view('admin.report.statistics',[
            'title' => 'STATISTICS REPORT INCOMING',
            "data" => $data,
            'date_range_start' => $date_start,
            'date_range_end' => $date_end,
            'request_type' => $request->request_type,
            'province' => $province
        ]);
    }

    public function erobReport(Request $request){
        if($request->isMethod('post') && isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $stored_name = "er_ob_report('$date_start','$date_end')";
        $data = \DB::connection('mysql')->select("call $stored_name");

        return view('admin.report.er_ob',[
            'title' => 'ER OB REPORT',
            "data" => $data,
            'date_range_start' => $date_start,
            'date_range_end' => $date_end
        ]);
    }

    public function averageUsersOnline(){
        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';

        $data = \DB::connection('mysql')->select("call average_login_month('$date_start','$date_end')");
        return view('admin.report.average_users_online',[
            "data" => $data
        ]);
    }

    public function sottoReports() {
        return view("admin.report.sotto_reports");
    }

    public function topIcd(Request $request) {
        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = '2022-01-13 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $icd = Icd::
                select("icd.icd_id","icd10.code","icd10.description",DB::raw("count(icd.icd_id) as count"))
                ->leftJoin("icd10","icd10.id","=","icd.icd_id")
                ->whereBetween("icd.created_at",[$date_start,$date_end])
                ->groupBy("icd.icd_id")
                ->OrderBY(DB::raw("count(icd.icd_id)"),"desc")
                ->limit(10)
                ->get();

        return view("admin.report.top_icd",[
            "icd" => $icd,
            "date_start" => $date_start,
            "date_end" => $date_end
        ]);
    }

    public function topReasonForReferral(Request $request) {
        if($request->date_range){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = '2022-01-13 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $pregnant_form = PregnantForm::
            select("reason_referral")
            ->whereNotNull("reason_referral")
            ->where("reason_referral","!=","-1")
            ->whereBetween("created_at",[$date_start,$date_end]);

        $union = PatientForm::
            select("reason_referral")
            ->whereNotNull("reason_referral")
            ->where("reason_referral","!=","-1")
            ->whereBetween("created_at",[$date_start,$date_end])
            ->unionAll($pregnant_form);

        $reason_for_referral = DB::table( DB::raw("({$union->toSql()}) as sub") )
            ->mergeBindings($union->getQuery())
            ->leftJoin("reason_referral","reason_referral.id","=","sub.reason_referral")
            ->select("reason_referral.id","reason_referral.reason",DB::raw("count(reason_referral.id) as count"))
            ->groupBy("reason_referral.id")
            ->OrderBY(DB::raw("count(reason_referral.id)"),"desc")
            ->get();

        return view("admin.report.top_reason_for_referral",[
            "reason_for_referral" => $reason_for_referral,
            "date_start" => $date_start,
            "date_end" => $date_end
        ]);
    }

}
