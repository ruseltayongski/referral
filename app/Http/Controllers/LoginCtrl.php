<?php

namespace App\Http\Controllers;

use App\Login;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LoginCtrl extends Controller
{
    public function index()
    {
        if($login = Session::get('auth')){
            return redirect($login->level);
        }

        return view('login');
    }

    public function validateLogin(Request $req)
    {
        $login = User::where('username',$req->username)
            ->first();
        if($login)
        {
            if(Hash::check($req->password,$login->password))
            {
                Session::put('auth',$login);
                $last_login = date('Y-m-d H:i:s');
                User::where('id',$login->id)
                    ->update([
                        'last_login' => $last_login,
                        'login_status' => 'login'
                    ]);
                $checkLastLogin = self::checkLastLogin($login->id);

                $l = new Login();
                $l->userId = $login->id;
                $l->login = $last_login;
                $l->logout = date("Y-m-d H:i:s",strtotime("0000-00-00 00:00:00"));
                $l->status = 'login';
                $l->type = $req->login_type;
                $l->login_link = $req->login_link;
                $l->save();

                if($checkLastLogin > 0 ){
                    Login::where('id',$checkLastLogin)
                        ->update([
                            'logout' => $last_login
                        ]);
                }

                if($login->status=='inactive'){
                    Session::forget('auth');
                    return Redirect::back()->with('error','Your account was deactivated by the administrator, please call 711 DOH health line.');
                }
                elseif($login->level=='doctor')
                    return redirect('doctor');
                else if($login->level=='chief')
                    return redirect('chief');
                else if($login->level=='support')
                    return redirect('support');
                else if($login->level=='mcc')
                    return redirect('mcc');
                else if($login->level=='admin')
                    return redirect('admin');
                else if($login->level=='eoc_region')
                    return redirect('eoc_region');
                else if($login->level=='eoc_city')
                    return redirect('eoc_city');
                else if($login->level=='opcen')
                    return redirect('opcen');
                else if($login->level=='bed_tracker')
                    return redirect('bed_tracker');
                else if($login->level=='midwife')
                    return redirect('midwife');
                else if($login->level=='medical_dispatcher')
                    return redirect('medical_dispatcher');
                else if($login->level=='nurse')
                    return redirect('nurse');
                else if($login->level=='vaccine')
                    return redirect('vaccine');
                else{
                    Session::forget('auth');
                    return Redirect::back()->with('error','You don\'t have access in this system.')->with('username',$req->username);
                }
            }
            else{
                return Redirect::back()->with('error','These credentials do not match our records')->with('username',$req->username);
            }
        }
        else{
            return Redirect::back()->with('error','These credentials do not match our records')->with('username',$req->username);
        }
    }

    function checkLastLogin($id)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();
        $login = Login::where('userId',$id)
                    ->whereBetween('login',[$start,$end])
                    ->orderBy('id','desc')
                    ->first();
        if($login && (!$login->logout>=$start && $login->logout<=$end)){
            return true;
        }

        if(!$login){
            return false;
        }

        return $login->id;
    }

    public function resetPassword(Request $req)
    {
        $user = Session::get('auth');
        if(Hash::check($req->current,$user->password))
        {
            if($req->newPass == $req->confirm){
                $lenght = strlen($req->newPass);
                if($lenght>=6)
                {
                    $password = bcrypt($req->newPass);
                    User::where('id',$user->id)
                        ->update([
                            'password' => $password
                        ]);
                    return 'changed';
                }else{
                    return 'length';
                }
            }else{
                return 'not_match';
            }
        }else{
            return 'error';
        }
    }

    public function updateToken($token){
        $user = Session::get("auth");
        Login::where("userId",$user->id)->where("login","like","%".date('Y-m-d')."%")->where("logout","0000-00-00 00:00:00")
                ->update([
                    "token" => $token
                ]);
    }

}
