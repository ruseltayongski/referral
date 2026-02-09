<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/laboratories', [ApiController::class, 'laboratoryList']);
Route::apiResource('laboratories', 'ApiController');
Route::post('icd10/search/{keyword}', 'TelemedicineApiCtrl@searchIcd10');
Route::post('check/labresult',[ApiController::class, 'checkLabResult']);
//api for telemedicine app
Route::post('telemed/login', 'TelemedicineApiCtrl@login');
Route::get('telemed/test', 'TelemedicineApiCtrl@test');
Route::get('telemed/appointment/calendar', 'TelemedicineApiCtrl@appointmentCalendar');
Route::get('telemed/appointment/getAppointment/{id}', 'TelemedicineApiCtrl@getAppointmentDetails');
Route::post('addPatient/store', 'TelemedicineApiCtrl@storePatient');
Route::post('doctor/refer/{type}', 'TelemedicineApiCtrl@referPatient');
Route::get('reasons_for_referral', 'TelemedicineApiCtrl@getReasonForReferral');
Route::get('tracker/activities/{facility_id}', 'TelemedicineApiCtrl@getTrackerDetailsTest');