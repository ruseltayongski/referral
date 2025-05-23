<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patients;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Login;

class CKDController extends Controller
{
    public function referFromCKD(Request $request,$patient_id = null)
    {
        $defaultToken = 'p4a6jgZJGP96GHhknTn2mKK6HSYT1clRqI1pHPBL8xhglULPY1xVPSfdi5w2';
        $bearerToken = $request->bearerToken();

        if ($bearerToken !== $defaultToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Instead of redirecting through validateLogin, let's handle this directly
        // Authenticate hardcoded user (you already have this logic)
        $user = User::where('username', 'rhu1-vicente')->first();
        
        if (!$user || !Hash::check('123', $user->password)) {
            return response()->json(['error' => 'User not found or invalid credentials'], 401);
        }       

        if ($user->status === 'inactive') {
            return response()->json(['error' => 'Your account was deactivated by the administrator, please call 711 DOH health line.'], 403);
        }

        // Set session and login tracking
        Session::put('auth', $user);
        $last_login = now();
        User::where('id', $user->id)->update([
            'last_login' => $last_login,
            'login_status' => 'login'
        ]);

        $checkLastLogin = Login::where('userId', $user->id)->where('status', 'login')->latest()->first();
        if ($checkLastLogin) {
            Login::where('id', $checkLastLogin->id)->update([
                'logout' => $last_login,
                'status' => 'logout'
            ]);
        }

        $login = new Login();
        $login->userId = $user->id;
        $login->login = $last_login;
        $login->status = 'login';
        $login->type = 'api';
        $login->login_link = 'validateToken';
        $login->save();

        $url = "https://ckd.cvchd7.com/ckd_referral";
        $json = file_get_contents($url);
        $allData = json_decode($json, true);
        $patients = collect($allData['patient'] ?? []);

        if ($patient_id) {
            $filtered = $patients->where('id', (int)$patient_id)->values();
            if ($filtered->isEmpty()) {
                   return response()->json(['message' => 'Data not found'], 404);
            }
        } else {
            $filtered = $patients;
        }

        // Manually paginate
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $data['patient']  = new LengthAwarePaginator(
            $currentItems,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('opcen.ckd_incoming', ['data' => $data,'trigger_ckd_info' => true]);
    }

    public function CKDIncoming(){
        $url = "https://ckd.cvchd7.com/ckd_referral";
        $json = file_get_contents($url);
        $allData = json_decode($json, true);
    
        $patients = $allData['patient'] ?? [];
    
       
        $perPage = 20;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
    
        $paginatedItems = array_slice($patients, $offset, $perPage);
    
        
        $data['patient'] = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            count($patients),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    
        return view('opcen.ckd_incoming', ['data' => $data,'trigger_ckd_info' => false]);
    }

    public function crossmatch(Request $request)
    {
        // Retrieve data from query parameters in the URL
        $validated = $request->validate([
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'contact_no' => 'nullable|string',
            'sex' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'barangay_id'=> 'nullable|integer'
        ]);
    
        $unique = array(
            $validated['first_name'],
            $validated['middle_name'],
            $validated['last_name'],
            date('Ymd', strtotime($validated['birth_date'])),
            $validated['barangay_id']
        );
        $unique = implode($unique);
    
        $mappedData = [
            'unique_id' => $unique,
            'fname' => $validated['first_name'] ?: '-',
            'mname' => $validated['middle_name'] ?: '-',
            'lname' => $validated['last_name'] ?: '-',
            'dob' => $validated['birth_date'] ?: now(),
            'contact' => $validated['contact_no'] ?: '-',
            'sex' => $validated['sex'] ?: '-',
            'civil_status' => $validated['civil_status'] ?: '-',
            'brgy' => $validated['barangay_id'] ?: '-',
            'muncity' => 82,
            'province' => 2,
            'region' => 'Region VII'
        ];
    
        // Search for existing patient using mapped fields
        $existing = Patients::where('fname', $mappedData['fname'])
            ->where('mname', $mappedData['mname'])
            ->where('lname', $mappedData['lname'])
            ->where('dob', $mappedData['dob'])
            ->where('contact', $mappedData['contact'])
            ->where('sex', $mappedData['sex'])
            ->first();
    
        if ($existing) {
            $existing->updated_at = now();
            $existing->save();
    
            return response()->json([
                'match' => 1,
                'data' => $existing
            ]);
        } else {
            $newPatient = Patients::create($mappedData);
    
            return response()->json([
                'match' => 0,
                'data' => $newPatient
            ]);
        }
    }
}
