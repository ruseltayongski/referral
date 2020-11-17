<?php

namespace App\Http\Controllers\Opcen;

use App\Barangay;
use App\ClientAddendum;
use App\Facility;
use App\Muncity;
use App\OpcenClient;
use App\Province;
use App\ReferenceNumber;
use App\RepeatCall;
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
        //for past 10 days
        $date_start = date('Y-m-d',strtotime(Carbon::now()->subDays(10))).' 00:00:00';
        $date_end = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        $past_days = \DB::connection('mysql')->select("call opcen_past_report_call('$date_start','$date_end')");
        ///

        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $new_call = OpcenClient::where("call_classification","new_call")
                ->whereBetween('time_started',[$startdate,$enddate])
                ->count();
            $data['new_call'][] = $new_call;

            $repeat_call = OpcenClient::where("call_classification","repeat_call")
                ->whereBetween('time_started',[$startdate,$enddate])
                ->count();
            $data['repeat_call'][] = $repeat_call;
        }

        $transaction_complete = OpcenClient::where("transaction_complete","!=",null)
            ->count();

        $transaction_incomplete = OpcenClient::where("transaction_incomplete","!=",null)
            ->count();

        $inquiry = OpcenClient::where("reason_calling","inquiry")
            ->count();

        $referral = OpcenClient::where("reason_calling","referral")
            ->count();

        $others = OpcenClient::where("reason_calling","others")
            ->count();

        return view('opcen.opcen',[
            "data" => $data,
            "transaction_complete" => $transaction_complete,
            "transaction_incomplete" => $transaction_incomplete,
            "inquiry" => $inquiry,
            "referral" => $referral,
            "others" => $others,
            "past_days" => $past_days
        ]);
    }

    public function opcenClient(Request $request){
        $seaarch = $request->search;
        $client = OpcenClient::where(function($q) use ($seaarch){
                $q->where('reference_number','like',"%$seaarch%")
                    ->orWhere('name','like',"%$seaarch%");
            })
            ->orderBy("time_started","desc")
            ->paginate(15);

        return view('opcen.client',[
            "client" => $client,
            "search" =>$seaarch
        ]);
    }

    public function bedAvailable(){
        return view('opcen.bed_available');
    }

    public function supplyReferenceNumber(){
        $encoded_by = Session::get('auth')->id;
        $reference_number = new ReferenceNumber();
        $reference_number->encoded_by = $encoded_by;
        $reference_number->save();
        return $reference_number->id;
    }

    public function newCall(){
        $province = Province::get();
        $facility = Facility::where("id","!=","63")->orderBy("name","asc")->get();
        Session::put("client",false); //from repeat call so that need to flush session
        return view('opcen.call',[
            "province" => $province,
            "facility" => $facility,
            "reference_number" => str_pad($this->supplyReferenceNumber(), 5, '0', STR_PAD_LEFT)
        ]);
    }

    public function repeatCall($client_id){
        $province = Province::get();
        $client = OpcenClient::find($client_id);
        $municipality = Muncity::where("province_id",$client->province_id)->get();
        $barangay = Barangay::where("muncity_id",$client->municipality_id)->get();
        $facility = Facility::where("id","!=","63")->orderBy("name","asc")->get();
        Session::put("client",$client);
        return view('opcen.call',[
            "province" => $province,
            "municipality" => $municipality,
            "barangay" => $barangay,
            "client" => $client,
            "facility" => $facility,
            "reference_number" => str_pad($this->supplyReferenceNumber(), 5, '0', STR_PAD_LEFT)
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
        $opcen_client->company = $request->company;
        $opcen_client->age = $request->age;
        $opcen_client->sex = $request->sex;
        $opcen_client->province_id = $request->province_id;
        $opcen_client->municipality_id = $request->municipality_id;
        $opcen_client->barangay_id = $request->barangay_id;
        $opcen_client->sitio = $request->sitio;
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

    public function clientInfo($client_id){
        $client = OpcenClient::find($client_id);
        return view('opcen.client_info',[
            "client" => $client
        ]);
    }

    public function addendumBody(){
        return view('opcen.client_addendum');
    }

    public function addendumPost(Request $request){
        foreach($request->addendum as $addendum){
            $client_addendum = new ClientAddendum();
            $client_addendum->reference_number = $request->reference_number;
            $client_addendum->client_id = $request->client_id;
            $client_addendum->encoded_by = Session::get('auth')->id;
            $client_addendum->notes = $addendum;
            $client_addendum->save();
        }
        Session::put("addendum",true);
        return Redirect::back();
    }

}
