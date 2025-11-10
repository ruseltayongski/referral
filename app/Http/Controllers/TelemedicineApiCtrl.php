<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Login;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TelemedicineApiCtrl extends Controller
{
    public function login(Request $req)
    {
        $login = User::where('username', $req->username)->first();
        $last_login = now(); // current timestamp

        if ($login && Hash::check($req->password, $login->password)) {

            User::where('id', $login->id)->update([
                'last_login' => $last_login,
                'login_status' => 'login'
            ]);

            $checkLastLogin = self::checkLastLogin($login->id);

            $l = new Login();
            $l->userId = $login->id;
            $l->login = $last_login;
            $l->logout = "0000-00-00 00:00:00";
            $l->status = 'login';
            $l->type = $req->login_type;
            $l->login_link = $req->login_link;
            $l->save();

            if ($checkLastLogin > 0) {
                Login::where('id', $checkLastLogin)->update([
                    'logout' => $last_login
                ]);
            }

            if ($login->level == 'doctor') {
                return response()->json([
                    'id' => $login->id,
                    'data' => $login
                ]);
            } else {
                return response()->json(['message' => 'not a doctor'], 403);
            }
        } else {
            return response()->json(['message' => 'invalid credentials'], 401);
        }
    }

    public function test()
    {
        return response()->json([
            'message' => 'api working'
        ]);
    }

    public function checkLastLogin($id)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $login = Login::where('userId', $id)
            ->whereBetween('login', [$start, $end])
            ->orderBy('id', 'desc')
            ->first();

        if ($login && !($login->logout >= $start && $login->logout <= $end)) {
            return true;
        }

        if (!$login) {
            return false;
        }

        return $login->id;
    }
}
