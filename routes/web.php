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

/*RHU Pages*/
Route::get('rhu','rhu\HomeCtrl@index');

Route::get('rhu/referral','rhu\ReferralCtrl@index');

Route::get('rhu/patient','rhu\PatientCtrl@index');
/*Hospital Pages*/

Route::get('login','LoginCtrl@index');
Route::post('login','LoginCtrl@validateLogin');
