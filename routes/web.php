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
    \Illuminate\Support\Facades\Session::flush();
    return redirect('login');
});
//SUPPORT Page
Route::get('support','support\HomeCtrl@index');

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
Route::get('doctor/chart','doctor\HomeCtrl@chart');

Route::get('doctor/referral','doctor\ReferralCtrl@index');
Route::get('doctor/referral/seen/{track_id}','doctor\ReferralCtrl@seen');//if the form is seen
Route::post('doctor/referral/reject/{track_id}','doctor\ReferralCtrl@reject');//if form is rejected
Route::post('doctor/referral/accept/{track_id}','doctor\ReferralCtrl@accept');//if form is accepted
Route::get('doctor/referral/call/{track_id}','doctor\ReferralCtrl@call');//if form is called
Route::post('doctor/referral/arrive/{track_id}','doctor\ReferralCtrl@arrive');//if patient is arrived
Route::post('doctor/referral/admit/{track_id}','doctor\ReferralCtrl@admit');//if patient is admitted
Route::post('doctor/referral/discharge/{track_id}','doctor\ReferralCtrl@discharge');//if patient is discharge
Route::post('doctor/referral/transfer/{track_id}','doctor\ReferralCtrl@transfer');//if patient is discharge


Route::get('doctor/referral/data/normal/{code}','doctor\ReferralCtrl@normalForm');
Route::get('doctor/referral/data/pregnant/{code}','doctor\ReferralCtrl@pregnantForm');

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

/*Hospital Pages*/

Route::get('login','LoginCtrl@index');
Route::post('login','LoginCtrl@validateLogin');
Route::post('reset/password','LoginCtrl@resetPassword');

/*Param */
//Route::get('uploadcsv','ParamCtrl@upload');
Route::get('location/barangay/{muncity_id}','LocationCtrl@getBarangay');
Route::get('location/facility/{facility_id}','LocationCtrl@facilityAddress');
Route::get('list/doctor/{facility_id}','ParamCtrl@getDoctorList');

Route::get('default','ParamCtrl@defaultTable');
Route::get('create/support','ParamCtrl@support');
//Route::get('user/create','UserCtrl@createUser');
//
Route::get('sample',function(){
    echo date('Y',strtotime("+1 year"));
});