<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patients;

class CKDController extends Controller
{
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
    
        return view('opcen.ckd_incoming', ['data' => $data]);
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
