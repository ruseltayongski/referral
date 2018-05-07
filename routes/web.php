<?php

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
    \App\User::where('id',$user->id)
            ->update([
                'login_status' => 'logout'
            ]);
    return redirect('login');
});
//ADMIN Page
Route::get('admin','admin\HomeCtrl@index');
Route::get('admin/chart','HomeCtrl@adminChart');
Route::get('admin/dashboard/count','admin\HomeCtrl@count');

Route::get('admin/login','admin\UserCtrl@loginAs');
Route::post('admin/login','admin\UserCtrl@assignLogin');
Route::get('admin/account/return','ParamCtrl@returnToAdmin');

//SUPPORT Page
Route::get('support','support\HomeCtrl@index');
Route::get('support/dashboard/count','support\HomeCtrl@count');

Route::get('support/users','support\UserCtrl@index');
Route::get('support/uers/add','support\UserCtrl@create');
Route::post('support/uers/add','support\UserCtrl@add');
Route::post('support/users/store','support\UserCtrl@store');
Route::post('support/users/update','support\UserCtrl@update');
Route::post('support/users/search','support\UserCtrl@search');

Route::get('support/users/check_username/{string}','support\UserCtrl@check');
Route::get('support/users/check_username/update/{string}/{user_id}','support\UserCtrl@checkUpdate');
Route::get('support/users/info/{user_id}','support\UserCtrl@info');

Route::get('support/hospital','support\HospitalCtrl@index');
Route::post('support/hospital/update','support\HospitalCtrl@update');


/*DOCTOR Pages*/
Route::get('doctor','doctor\HomeCtrl@index');

Route::get('doctor/referral','doctor\ReferralCtrl@index');
Route::post('doctor/referral','doctor\ReferralCtrl@searchReferral');

Route::get('doctor/referral/seen/{track_id}','doctor\ReferralCtrl@seen');//if the form is seen
Route::post('doctor/referral/reject/{track_id}','doctor\ReferralCtrl@reject');//if form is rejected
Route::post('doctor/referral/accept/{track_id}','doctor\ReferralCtrl@accept');//if form is accepted
Route::get('doctor/referral/call/{activity_id}','doctor\ReferralCtrl@call');//if form is called
Route::get('doctor/referral/calling/{track_id}','doctor\ReferralCtrl@calling');//if form is called
Route::post('doctor/referral/arrive/{track_id}','doctor\ReferralCtrl@arrive');//if patient is arrived
Route::post('doctor/referral/admit/{track_id}','doctor\ReferralCtrl@admit');//if patient is admitted
Route::post('doctor/referral/discharge/{track_id}','doctor\ReferralCtrl@discharge');//if patient is discharge
Route::post('doctor/referral/transfer/{track_id}','doctor\ReferralCtrl@transfer');//if patient is discharge
Route::post('doctor/referral/redirect/{activity_id}','doctor\ReferralCtrl@redirect');//if patient is discharge


Route::get('doctor/referral/data/normal/{id}','doctor\ReferralCtrl@normalForm');
Route::get('doctor/referral/data/pregnant/{id}','doctor\ReferralCtrl@pregnantForm');

Route::get('doctor/referred','doctor\ReferralCtrl@referred');

Route::get('doctor/patient','doctor\PatientCtrl@index');
Route::post('doctor/patient','doctor\PatientCtrl@searchProfile');

Route::get('doctor/patient/info/{id}','doctor\PatientCtrl@showPatientProfile');
Route::get('doctor/patient/add','doctor\PatientCtrl@addPatient');
Route::post('doctor/patient/store','doctor\PatientCtrl@storePatient');

Route::post('doctor/patient/refer/{type}','doctor\PatientCtrl@referPatient');

Route::get('doctor/accepted','doctor\PatientCtrl@accepted');

Route::get('doctor/patient/tsekapinfo/{id}','doctor\PatientCtrl@showTsekapProfile');
Route::get('doctor/patient/tsekap','doctor\PatientCtrl@tsekap');
Route::post('doctor/patient/tsekap','doctor\PatientCtrl@searchTsekap');

Route::get('doctor/report','ParamCtrl@maintenance');

Route::get('doctor/print/form/{track_id}','doctor\PrintCtrl@printReferral');


Route::get('doctor/list','doctor\UserCtrl@index');
Route::post('doctor/list','doctor\UserCtrl@searchDoctor');

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
Route::get('location/facility/{facility_id}','LocationCtrl@facilityAddress');
Route::get('list/doctor/{facility_id}','ParamCtrl@getDoctorList');

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

Route::get('sample',function(){
//    $pdf = new Fpdf();
//    $pdf::AddPage();
//    $pdf::SetFont('Arial','B',18);
//    $pdf::Cell(0,10,"Title",0,"","C");
//    $pdf::Ln();
//    $pdf::Ln();
//    $pdf::SetFont('Arial','B',12);
//    $pdf::cell(25,8,"ID",1,"","C");
//    $pdf::cell(45,8,"Name",1,"","L");
//    $pdf::cell(35,8,"Address",1,"","R");
//    $pdf::Ln();
//    $pdf::SetFont("Arial","",10);
//    $pdf::cell(25,8,"1",1,"","C");
//    $pdf::cell(45,8,"John",1,"","L");
//    $pdf::cell(35,8,"New York",1,"","L");
//    $pdf::Ln();
//
//    $x = $pdf::GetX();
//    $y = $pdf::GetY();
//
//    $col1="PILOT REMARKS\n\n";
//    $pdf::MultiCell(100, 10, $col1, 1, 1);
//
//    $pdf::SetXY($x + 189, $y);
//
//    $col2="Pilot's Name and Signature\nJimmy Lomocso";
//    $pdf::MultiCell(63, 10, $col2, 1);
//    $pdf::Ln(0);
//    $col3="Date Prepared\nMay 4, 2018";
//    $pdf::MultiCell(63, 10, $col3, 1);
//
//    $pdf::Output();
    Fpdf::AddPage();
    Fpdf::SetFont('Arial','B',18);
    Fpdf::Cell(0,10,"Title",0,"","C");
    Fpdf::Output();
    exit;
});

