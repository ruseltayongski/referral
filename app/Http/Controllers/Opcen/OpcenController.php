<?php

namespace App\Http\Controllers\Opcen;

use App\Barangay;
use App\Muncity;
use App\OpcenClient;
use App\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inventory;
use Illuminate\Support\Facades\Session;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Redirect;
use DateTime;
use Carbon\Carbon;

class OpcenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function opcenDashboard(){
        $user = Session::get('auth');
        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $new_call = OpcenClient::where("encoded_by",$user->id)
                ->where("call_classification","new_call")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->get();
            $data['new_call'][] = count($new_call);

            $repeat_call = OpcenClient::where("encoded_by",$user->id)
                ->where("call_classification","repeat_call")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->get();
            $data['repeat_call'][] = count($repeat_call);
        }

        return view('opcen.opcen',[
            "data" => $data
        ]);
    }

    public function opcenClient(Request $request){
        $seaarch = $request->search;
        $user = Session::get('auth');
        $client = OpcenClient::where("encoded_by",$user->id)
            ->where(function($q) use ($seaarch){
                $q->where('reference_number','like',"%$seaarch%")
                    ->orWhere('name','like',"%$seaarch%");
            })
            ->paginate(15);

        return view('opcen.client',[
            "client" => $client,
            "search" =>$seaarch
        ]);
    }

    public function bedAvailable(){
        return view('opcen.bed_available');
    }

    public function newCall(){
        $province = Province::get();
        Session::put("client",false);
        return view('opcen.call',[
            "province" => $province
        ]);
    }

    public function repeatCall($client_id){
        $province = Province::get();
        $client = OpcenClient::find($client_id);
        $municipality = Muncity::where("province_id",$client->province_id)->get();
        $barangay = Barangay::where("muncity_id",$client->municipality_id)->get();
        Session::put("client",$client);
        return view('opcen.call',[
            "province" => $province,
            "municipality" => $municipality,
            "barangay" => $barangay,
            "client" => $client
        ]);
    }

    public function reasonCalling($reason){
        return view('opcen.reason_calling',[
            "reason" => $reason,
        ]);
    }

    public function transactionComplete(){
        return view('opcen.transaction_complete');
    }

    public function transactionInComplete(){
        return view('opcen.transaction_incomplete');
    }

    public function availabilityAndService(){
        $inventory = Inventory::select("inventory.*","facility.name as facility","facility.province as province","facility.id as facility_id")
            ->leftJoin("facility","facility.id","=","inventory.facility_id")
            ->where("facility.name","not like","%RHU%")
            ->where("facility.name","not like","%department%")
            ->where("facility.name","not like","%referred%")
            ->orderBy("facility.province","asc")
            ->orderBy("facility.name","asc")
            ->orderBy("inventory.name","asc")
            ->get();

        Session::put("inventory",$inventory);

        return view('eoc.eoc_region',[
            "inventory" => $inventory
        ]);
    }

    public function sendSMS(){
        // Your Account SID and Auth Token from twilio.com/console
        $account_sid = 'AC3cdea35db1d24c05750207bf434c46ea';
        $auth_token = '351ea813ced84cb56a2102d95df47ca6';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

        // A Twilio number you own with SMS capabilities
        $twilio_number = "+19123485641";

        $client = new Client($account_sid, $auth_token);
        $client->messages->create(
        // Where to send a text message (your cell phone?)
            '+639238309990',
            array(
                'from' => $twilio_number,
                'body' => "\n".date('m/d/Y H:i:s')."\nDaghang salamat sa pag tawag sa DOH HealthLine. Amping!"
            )
        );
    }

    public function onChangeProvince($province_id){
        return Muncity::select("id","description")->where("province_id","=",$province_id)->get();
    }

    public function onChangeMunicipality($municipality_id){
        return Barangay::select("id","description")->where("muncity_id","=",$municipality_id)->get();
    }

    public function transactionEnd(Request $request){
        $time_started = date("Y-m-d H:i:s",strtotime($request->time_started));
        $time_ended = date("Y-m-d H:i:s",strtotime($request->time_ended));
        $start_date = new DateTime($time_started);
        $duration_time = $start_date->diff(new DateTime($time_ended));
        $encoded_by = Session::get('auth')->id;

        $opcen_client = new OpcenClient();
        $opcen_client->reference_number = $request->reference_number;
        $opcen_client->encoded_by = $encoded_by;
        $opcen_client->call_classification = $request->call_classification;
        $opcen_client->reference_number = $request->reference_number;
        $opcen_client->name = $request->name;
        $opcen_client->age = $request->age;
        $opcen_client->sex = $request->sex;
        $opcen_client->province_id = $request->province_id;
        $opcen_client->municipality_id = $request->municipality_id;
        $opcen_client->barangay_id = $request->barangay_id;
        $opcen_client->contact_number = $request->contact_number;
        $opcen_client->relationship = $request->relationship;
        $opcen_client->reason_calling = $request->reason_calling;
        $opcen_client->reason_notes = $request->reason_notes;
        $opcen_client->reason_patient_data = $request->reason_patient_data;
        $opcen_client->reason_chief_complains = $request->reason_chief_complains;
        $opcen_client->reason_action_taken = $request->reason_action_taken;
        $opcen_client->transaction_complete = $request->transaction_complete;
        $opcen_client->transaction_incomplete = $request->transaction_incomplete;
        $opcen_client->time_started = $time_started;
        $opcen_client->time_ended = $time_ended;
        $opcen_client->time_duration = $duration_time->h.':'.$duration_time->i.':'.$duration_time->s;
        $opcen_client->save();

        Session::put('opcen',true);
        return Redirect::back();
    }


}
