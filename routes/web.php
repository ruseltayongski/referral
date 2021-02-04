<?php
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeCtrl@index');
Route::get('logout', function(){
    $user = \Illuminate\Support\Facades\Session::get('auth');
    \Illuminate\Support\Facades\Session::flush();
    if(isset($user)){
        \App\User::where('id',$user->id)
            ->update([
                'login_status' => 'logout'
            ]);
        $logout = date('Y-m-d H:i:s');
        $logoutId = \App\Login::where('userId',$user->id)
            ->orderBy('id','desc')
            ->first()
            ->id;

        \App\Login::where('id',$logoutId)
            ->update([
                'status' => 'login_off',
                'logout' => $logout
            ]);
    }
    return redirect('login');
});
Route::get('login_expire', function(){
    \Illuminate\Support\Facades\Session::flush();
    return redirect('login');
});

//ADMIN Page
Route::get('admin','doctor\HomeCtrl@index');
Route::get('admin/chart','HomeCtrl@adminChart');
Route::get('admin/dashboard/count','admin\HomeCtrl@count');

Route::get('admin/users','admin\UserCtrl@index');
Route::match(['GET','POST'],'admin/facility','admin\FacilityCtrl@index');
Route::post('admin/facility/add','admin\FacilityCtrl@FacilityAdd');
Route::post('admin/facility/body','admin\FacilityCtrl@FacilityBody');
Route::post('admin/facility/delete','admin\FacilityCtrl@FacilityDelete');

//PROVINCE

//MUNICIPALITY

//BARANGAY


Route::post('admin/users/store','admin\UserCtrl@store');
Route::post('admin/users/update','admin\UserCtrl@update');

Route::get('admin/users/info/{user_id}','admin\UserCtrl@info');
Route::get('admin/users/check_username/{string}','admin\UserCtrl@check');

Route::get('admin/login','admin\UserCtrl@loginAs');
Route::post('admin/login','admin\UserCtrl@assignLogin');
Route::get('admin/account/return','ParamCtrl@returnToAdmin');

Route::get('admin/report/online','admin\ReportCtrl@online1');
Route::post('admin/report/online','admin\ReportCtrl@filterOnline1');

Route::get('admin/report/referral','admin\ReportCtrl@referral');
Route::post('admin/report/referral','admin\ReportCtrl@filterReferral');

Route::get('admin/report/patient/incoming','admin\PatientCtrl@incoming');
Route::post('admin/report/patient/incoming','admin\PatientCtrl@incomingDateRange');
Route::get('admin/report/patient/outgoing','admin\PatientCtrl@outgoing');
Route::get('admin/daily/referral/incoming/{province_id}','admin\PatientCtrl@getAddress');

//consolidated
Route::get('admin/report/consolidated/incoming','admin\PatientCtrl@consolidatedIncoming');
Route::match(['GET','POST'],'admin/report/consolidated/incomingv2','admin\PatientCtrl@consolidatedIncomingv2');
Route::get('admin/no_action/{facility_id}/{date_start}/{date_end}/{type}','admin\PatientCtrl@NoAction');
Route::get('admin/report/consolidated/outgoing','admin\PatientCtrl@consolidatedOutgoing');


Route::get('admin/daily/users','admin\DailyCtrl@users');
Route::post('admin/daily/users','admin\DailyCtrl@usersFilter');
Route::get('admin/daily/users/export','admin\ExportCtrl@dailyUsers');

Route::get('admin/daily/referral','admin\DailyCtrl@referral');
Route::get('admin/daily/referral/incoming/','admin\DailyCtrl@incoming');
Route::get('admin/daily/referral/outgoing','admin\DailyCtrl@outgoing');
Route::post('admin/daily/referral','admin\DailyCtrl@referralFilter');
Route::get('admin/daily/referral/export','admin\ExportCtrl@dailyReferral');
Route::match(['GET','POST'],'admin/statistics/incoming','admin\ReportCtrl@statisticsReportIncoming');
Route::match(['GET','POST'],'admin/statistics/outgoing','admin\ReportCtrl@statisticsReportOutgoing');
Route::match(['GET','POST'],'admin/er_ob','admin\ReportCtrl@erobReport');
Route::match(['GET','POST'],'admin/average/user_online','admin\ReportCtrl@averageUsersOnline');

//SUPPORT Page
Route::get('support','doctor\HomeCtrl@index');
Route::get('support/dashboard/count','support\HomeCtrl@count');

Route::get('support/users','support\UserCtrl@index');
Route::get('support/uers/add','support\UserCtrl@create');
Route::post('support/uers/add','support\UserCtrl@add');
Route::post('support/users/store','support\UserCtrl@store');
Route::post('support/users/update','support\UserCtrl@update');
//Route::post('support/users/search','support\UserCtrl@search'); JIMMY CODE

Route::get('support/users/check_username/{string}','support\UserCtrl@check');
Route::get('support/users/check_username/update/{string}/{user_id}','support\UserCtrl@checkUpdate');
Route::get('support/users/info/{user_id}','support\UserCtrl@info');

Route::get('support/hospital','support\HospitalCtrl@index');
Route::post('support/hospital/update','support\HospitalCtrl@update');

Route::get('support/report/users','support\ReportCtrl@users');
Route::post('support/report/users','support\ReportCtrl@usersFilter');
Route::get('support/report/users/export','support\ExportCtrl@dailyUsers');

Route::get('support/report/referral','support\ReportCtrl@referral');
Route::post('support/report/referral','support\ReportCtrl@referralFilter');
Route::get('support/report/referral/export','support\ExportCtrl@dailyReferral');

Route::get('support/report/incoming','support\ReportCtrl@incoming');

Route::get('support/chat','support\ChatCtrl@index');
Route::post('support/chat','support\ChatCtrl@send');
Route::get('support/chat/messages','support\ChatCtrl@messages');
Route::get('support/chat/messages/load','support\ChatCtrl@loadMessages');
Route::get('support/chat/messages/reply/{id}','support\ChatCtrl@reply');
Route::get('support/chat/sample','support\ChatCtrl@sample');

/*DOCTOR Pages*/
Route::get('doctor','doctor\HomeCtrl@index');

Route::get('doctor/referral','doctor\ReferralCtrl@index');
Route::post('doctor/referral','doctor\ReferralCtrl@searchReferral');

Route::get('doctor/referral/seen/{track_id}','doctor\ReferralCtrl@seen');//if the form is seen
Route::get('doctor/referral/seenBy/{track_id}','doctor\ReferralCtrl@seenBy');//if the form is seen
Route::get('doctor/referral/seenBy/list/{track_id}','doctor\ReferralCtrl@seenByList');//if the form is seen
Route::get('doctor/referral/callerBy/list/{track_id}','doctor\ReferralCtrl@callerByList');//if the form is called
Route::post('doctor/referral/reject/{track_id}','doctor\ReferralCtrl@reject');//if form is rejected
Route::post('doctor/referral/accept/{track_id}','doctor\ReferralCtrl@accept');//if form is accepted
Route::get('doctor/referral/call/{activity_id}','doctor\ReferralCtrl@call');//if form is called
Route::get('doctor/referral/calling/{track_id}','doctor\ReferralCtrl@calling');//if form is called
Route::post('doctor/referral/arrive/{track_id}','doctor\ReferralCtrl@arrive');//if patient is arrived
Route::post('doctor/referral/archive/{track_id}','doctor\ReferralCtrl@archive');//if patient is archived
Route::post('doctor/referral/admit/{track_id}','doctor\ReferralCtrl@admit');//if patient is admitted
Route::post('doctor/referral/discharge/{track_id}','doctor\ReferralCtrl@discharge');//if patient is discharge
Route::post('doctor/referral/transfer/{track_id}','doctor\ReferralCtrl@transfer');//if patient is discharge
Route::post('doctor/referral/redirect/{activity_id}','doctor\ReferralCtrl@redirect');//if patient is discharge


Route::get('doctor/referral/data/normal/{id}','doctor\ReferralCtrl@normalForm');
Route::get('doctor/referral/data/pregnant/{id}','doctor\ReferralCtrl@pregnantForm');

Route::match(["GET","POST"],'doctor/referred','doctor\ReferralCtrl@referred');
Route::get('doctor/referred2','doctor\ReferralCtrl@referred2');
Route::post('doctor/referred/cancel/{id}','doctor\ReferralCtrl@cancelReferral');
Route::post('doctor/referred/transfer/{id}','doctor\ReferralCtrl@transferReferral');
Route::post('doctor/referred/search','doctor\ReferralCtrl@searchReferred');
Route::match(["get","post"],'doctor/referred/track','doctor\ReferralCtrl@trackReferral');

Route::get('doctor/patient','doctor\PatientCtrl@index');
Route::post('doctor/patient','doctor\PatientCtrl@searchProfile');

Route::get('doctor/patient/info/{id}','doctor\PatientCtrl@showPatientProfile');
Route::get('doctor/patient/add','doctor\PatientCtrl@addPatient');
Route::post('doctor/patient/store','doctor\PatientCtrl@storePatient');
Route::post('doctor/patient/update','doctor\PatientCtrl@updatePatient');
Route::post('doctor/patient/update','doctor\PatientCtrl@updatePatient');

Route::post('doctor/patient/refer/walkin/{type}','doctor\PatientCtrl@referPatientWalkin');
Route::post('doctor/patient/refer/{type}','doctor\PatientCtrl@referPatient');


Route::get('doctor/accepted','doctor\PatientCtrl@accepted');
Route::post('doctor/accepted','doctor\PatientCtrl@searchAccepted');

Route::get('doctor/discharge','doctor\PatientCtrl@discharge');
Route::post('doctor/discharge','doctor\PatientCtrl@searchDischarged');

Route::get('doctor/cancelled','doctor\PatientCtrl@cancel');
Route::post('doctor/cancelled','doctor\PatientCtrl@searchCancelled');

Route::get('doctor/archived','doctor\PatientCtrl@archived');
Route::post('doctor/archived','doctor\PatientCtrl@searchArchived');

Route::get('doctor/feedback/{code}','doctor\ReferralCtrl@feedback');
Route::get('doctor/feedback/reply/{id}','doctor\ReferralCtrl@replyFeedback');
Route::get('doctor/feedback/load/{code}','doctor\ReferralCtrl@loadFeedback');
Route::get('doctor/feedback/notification/{code}/{user_id}','doctor\ReferralCtrl@notificationFeedback');
Route::post('doctor/feedback','doctor\ReferralCtrl@saveFeedback');

Route::get('doctor/history/{code}','doctor\PatientCtrl@history');

Route::get('doctor/patient/tsekapinfo/{id}','doctor\PatientCtrl@showTsekapProfile');
Route::get('doctor/patient/tsekap','doctor\PatientCtrl@tsekap');
Route::post('doctor/patient/tsekap','doctor\PatientCtrl@searchTsekap');

Route::get('doctor/print/form/{track_id}','doctor\PrintCtrl@printReferral');


Route::get('doctor/list','doctor\UserCtrl@index');
Route::post('doctor/list','doctor\UserCtrl@searchDoctor');

Route::post('doctor/change/login','doctor\UserCtrl@changeLogin');

Route::get('doctor/verify/{code}','ParamCtrl@verifyCode');

Route::get('/doctor/report/incoming','doctor\ReportCtrl@incoming');
Route::post('/doctor/report/incoming','doctor\ReportCtrl@filterIncoming');
Route::get('/doctor/report/outgoing','doctor\ReportCtrl@outgoing');
Route::post('/doctor/report/outgoing','doctor\ReportCtrl@filterOutgoing');

Route::get('duty/{option}','UserCtrl@duty');
/*Hospital Pages*/

Route::get('login','LoginCtrl@index');
Route::post('login','LoginCtrl@validateLogin');
Route::post('reset/password','LoginCtrl@resetPassword');
Route::get('maintenance',function(){
    return view('error',['title' => 'Maintenance']);
});

/*Param */
Route::get('chart','HomeCtrl@chart');
//Route::get('uploadcsv','ParamCtrl@upload');
Route::get('location/barangay/{muncity_id}','LocationCtrl@getBarangay');
Route::get('location/barangay/{province_id}/{muncity_id}','LocationCtrl@getBarangay1');
Route::get('location/muncity/{province_id}','LocationCtrl@getMuncity');
Route::get('location/facility/{facility_id}','LocationCtrl@facilityAddress');
Route::get('list/doctor/{facility_id}/{department_id}','ParamCtrl@getDoctorList');

//Route::get('default','ParamCtrl@defaultTable');
//Route::get('create/support','ParamCtrl@support');
Route::get('create/admin','ParamCtrl@admin');
//Route::get('user/create','UserCtrl@createUser');
//Route::get('user/update',function(){
//    \App\User::where('id','!=',0)
//        ->update([
//            'password' => bcrypt('123')
//        ]);
//    \App\Facility::where('id','!=',0)
//        ->update([
//            'status' => 1
//        ]);
//    echo 'Passwords updated!';
//});
//

Route::get('sample','HomeCtrl@sample');

//reset password
Route::get('resetPassword/{username}',function($username){
    $user = \App\User::where('username','=',$username)->first();
    if($user){
        $password = bcrypt('123');
        $user->update([
            'password' => $password
        ]);
        return 'Successfully Reset Password';
    } else {
        return 'Failed Reset Password';
    }
});

//API
Route::get('api','ApiController@api');

Route::get('/token/save/{token}','DeviceTokenCtrl@save');
Route::get('/token/send/{title}/{body}/{token}','DeviceTokenCtrl@send');

Route::get('/fcm/send','FcmCtrl@send');
Route::get('/doctor/name/{id}','ParamCtrl@getDoctorName');

//MANAGE MCC PAGE
Route::get('/mcc','doctor\HomeCtrl@index');
Route::get('/mcc/dashboard/count','mcc\HomeCtrl@count');
Route::get('/mcc/report/online','mcc\ReportCtrl@online');
Route::post('/mcc/report/online','mcc\ReportCtrl@filterOnline');
Route::get('/mcc/report/incoming','mcc\ReportCtrl@incoming');
Route::post('/mcc/report/incoming','mcc\ReportCtrl@filterIncoming');
Route::get('/mcc/report/timeframe','mcc\ReportCtrl@timeframe');
Route::post('/mcc/report/timeframe','mcc\ReportCtrl@filterTimeframe');
Route::get('/mcc/track','mcc\ReportCtrl@trackReferral');
Route::post('/mcc/track','mcc\ReportCtrl@searchTrackReferral');


//FEEDBACK
Route::get('feedback/home','FeedbackCtrl@home');
Route::post('feedback/comment_append','FeedbackCtrl@CommentAppend');

//EXCEL
Route::get('excel/incoming','ExcelCtrl@ExportExcelIncoming');
Route::get('excel/outgoing','ExcelCtrl@ExportExcelOutgoing');
Route::get('excel/all','ExcelCtrl@ExportExcelAll');
Route::match(['GET','POST'],'excel/import','ExcelCtrl@importExcel');

//GRAPH
Route::get("admin/report/graph/incoming","admin\ReportCtrl@graph");
Route::get("admin/report/graph/bar_chart","admin\ReportCtrl@bar_chart");

//INSERT INTO ACTIVITY(ACCEPTED) BUGS IN ACCEPT
Route::get("insert_activity",function(){
    $bugs_code = [
        "191006-004-124959",
    ];
    foreach($bugs_code as $value){
        $tracking = \App\Tracking::where("code",$value)->first();
        $user = \App\User::where("id",$tracking->action_md)->first();
        $data = array(
            'code' => $tracking->code,
            'patient_id' => $tracking->patient_id,
            'date_referred' => $tracking->date_accepted,
            'referred_from' => $tracking->referred_from,
            'referred_to' => $user->facility_id,
            'department_id' => $user->department_id,
            'referring_md' => $tracking->referring_md,
            'action_md' => $user->id,
            'remarks' => $tracking->remarks,
            'status' => "accepted"
        );
        \App\Activity::create($data);
    }

    return "Successfully Updated!";
});

//API RUSEL
Route::get('gbm9Ti6UBpT5K2P5qQ5bD0OMhvxJnYNZ/{offset}/{limit}','ApiController@getActivity'); //GET ACTIVITY
Route::get('bHDMSB83RwoznXAcnnC6aFtqiL1djvJs','ApiController@getBaby'); //GET BABY
Route::get('xZzl92SjyZPkGQOaLzsQhE9PFIvfjmil','ApiController@getBarangay'); //GET BARANGAY
Route::get('XO2XFSiDX2PdHyLbq9WNHhA95vy3Fdld','ApiController@getDepartment'); //GET DEPARTMENT
Route::get('iMkiW5YcHA6D9Gd7BuTteeQPVx4a1UxK','ApiController@getFacility'); //GET FACILITY
Route::get('Cj7lhsInOGIvKKdpHB3kIhrectxLgTeU','ApiController@getFeedback'); //GET FEEDBACK
Route::get('wcuoLqAqKQGw6yl9SxX9vZ6ieZzG9HaA','ApiController@getIcd10'); //GET ICD10
Route::get('nvOGql1zXiEirNkXtPm7udIFsIaxBndB','ApiController@getIssue'); //GET ISSUE
Route::get('q8d8Jh1KoC4ac6t1ksaGH0J4TcMTmazM/{offset}/{limit}','ApiController@getLogin'); //GET LOGIN
Route::get('Rcha066KNYeBt10dvjgRjPPU04q4b9Ob','ApiController@getModeTransportation'); //GET MODE TRANSPORTATION
Route::get('J9bXjSR50dZHHEuJ65qOLAWuor4x4Ztn','ApiController@getMuncity'); //GET MUNCITY
Route::get('DOitGyz7gKVWWJ3IqjYA5ioLc1qbiEei/{offset}/{limit}','ApiController@getPatientForm'); //GET PATIENT FORM
Route::get('WN3woYd8ZlxutRXg2B7Ud1qnEGqx7FSK/{offset}/{limit}','ApiController@getPatients'); //GET PATIENTS
Route::get('Z9tE1ihdu37imqmVTSL3I8qOiotEwIla/{offset}/{limit}','ApiController@getPregnantForm'); //GET PREGNANT FORM
Route::get('3efFgpkQFg56lZGtOp6WzmkXXBsGfPx9','ApiController@getProvince'); //GET PROVINCE
Route::get('XVup1R4fVdnSnYFvUWSMX5FTmyHkbn5p','ApiController@getSeen'); //GET SEEN
Route::get('4PXhMnFe3O8wzVg1I3fu4t53W5zjMOqA/{offset}/{limit}','ApiController@getTracking'); //GET TRACKING
Route::get('IMG7uSgZBKB9jW6KhMT8N4QAV2Ia5PUL','ApiController@getUsers'); //GET USERS
//

//online facility
Route::match(['GET','POST'],"online/facility","admin\ReportCtrl@onlineFacility");
Route::match(['GET','POST'],"offline/facility","admin\ReportCtrl@offlineFacility");

//onboard facility
Route::get("onboard/facility","admin\ReportCtrl@onboardFacility");
Route::get("onboard/users","admin\ReportCtrl@onboardUsers");

//EocRegion dashboard
Route::get('eoc_region','Eoc\HomeController@EocRegion');
Route::get('eoc_region/bed/{facility_id}','Eoc\HomeController@bed');
Route::post('eoc_region/bed/add','Eoc\HomeController@bedAdd');

//EOC CITY
Route::get('eoc_city','Eoc\HomeController@EocCity');
Route::match(['POST','GET'],'eoc_city/graph','Eoc\HomeController@Graph');
Route::get('eoc_city/excel','ExcelCtrl@EocExcel');

//OPCEN
Route::get('opcen','Opcen\OpcenController@opcenDashboard');
Route::get('opcen/client','Opcen\OpcenController@opcenClient');
Route::get('opcen/client/addendum/body','Opcen\OpcenController@addendumBody');
Route::post('opcen/client/addendum/post','Opcen\OpcenController@addendumPost');
Route::get('opcen/client/form/{client_id}','Opcen\OpcenController@clientInfo');
Route::get('opcen/bed/available','Opcen\OpcenController@bedAvailable');
Route::get('opcen/new_call','Opcen\OpcenController@newCall');
Route::get('opcen/repeat_call/{client_id}','Opcen\OpcenController@repeatCall');
Route::get('opcen/reason_calling/{reason}','Opcen\OpcenController@reasonCalling');
Route::get('opcen/availability/service','Opcen\OpcenController@availabilityAndService');
Route::get('opcen/sms','Opcen\OpcenController@sendSMS');
Route::get('opcen/transaction/complete','Opcen\OpcenController@transactionComplete');
Route::get('opcen/transaction/incomplete','Opcen\OpcenController@transactionInComplete');
Route::get('opcen/onchange/province/{province_id}','Opcen\OpcenController@onChangeProvince');
Route::get('opcen/onchange/municipality/{municipality_id}','Opcen\OpcenController@onChangeMunicipality');
Route::post('opcen/transaction/end','Opcen\OpcenController@transactionEnd');

//Inventory
Route::get('inventory/{facility_id}','Eoc\InventoryController@Inventory');
Route::get('inventory/append/{facility_id}','Eoc\InventoryController@appendInventory');
Route::post('inventory/update/page','Eoc\InventoryController@inventoryUpdatePage');
Route::post('inventory/update/save','Eoc\InventoryController@inventoryUpdateSave');
Route::post('inventory/insert','Eoc\InventoryController@insertInventory');

//chat
Route::get('/chat', 'ContactsController@index')->name('home');
Route::get('/contacts', 'ContactsController@get');
Route::get('/conversation/{id}', 'ContactsController@getMessagesFor');
Route::post('/conversation/send', 'ContactsController@send');

//set logout
Route::post('/logout/set','doctor\UserCtrl@setLogoutTime');

//bed tracker
Route::get('bed/{facility_id}','BedTrackerCtrl@bed');
Route::post('bed_update','BedTrackerCtrl@bedUpdate');
Route::get('bed_admin','BedTrackerCtrl@bedAdmin');
Route::get('bed_tracker','BedTrackerCtrl@home');
Route::get('bed_tracker/select/facility/{province_id}','BedTrackerCtrl@selectFacility');

//monitoring
Route::match(['GET','POST'],'monitoring','Monitoring\MonitoringCtrl@monitoring');
Route::post('monitoring/remark','Monitoring\MonitoringCtrl@bodyRemark');
Route::post('monitoring/add/remark','Monitoring\MonitoringCtrl@addRemark');
Route::get('monitoring/feedback/{code}','Monitoring\MonitoringCtrl@feedbackDOH');

//walkin
Route::match(['GET','POST'],'patient/walkin','doctor\PatientCtrl@walkinPatient');

//issue and concern
Route::post('issue/concern/submit','Monitoring\MonitoringCtrl@issueSubmit');
Route::match(['GET','POST'],'issue/concern','Monitoring\MonitoringCtrl@getIssue');
Route::get('issue/concern/{tracking_id}/{referred_from}','Monitoring\MonitoringCtrl@IssueAndConcern');

//midwife
Route::get('midwife','doctor\HomeCtrl@index');

//ICD 10
Route::post('icd/search','icd\IcdController@icdSearch');
Route::get('icd/get','icd\IcdController@index');
