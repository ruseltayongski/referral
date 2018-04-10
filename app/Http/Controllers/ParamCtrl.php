<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Baby;
use App\Facility;
use App\PatientForm;
use App\PregnantForm;
use App\Tracking;
use App\User;
use Illuminate\Http\Request;

class ParamCtrl extends Controller
{
    static function getAge($date){
        //date in mm/dd/yyyy format; or it can be in other formats as well
        $birthDate = date('m/d/Y',strtotime($date));
        //explode the date to get month, day and year
        $birthDate = explode("/", $birthDate);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
            ? ((date("Y") - $birthDate[2]) - 1)
            : (date("Y") - $birthDate[2]));
        return $age;
    }

    function getDoctorList($facility_id)
    {
        $user = User::select('id','fname','mname','lname','contact')
            ->where('facility_id',$facility_id)
            ->where('level','doctor')
            ->get();
        return $user;
    }

    public function upload()
    {
        $file = fopen(url('public/facility.csv'),'r');
        $data = array();
        while(! feof($file))
        {
            $data[] = fgetcsv($file);
        }
        $count = 1;
        foreach($data as $row)
        {
            if($count==1){
                $count=0;
                continue;
            }
            $facility = array(
                'province' => $row[0],
                'name' => $row[1],
                'contact' => $row[2],
                'email' => $row[3]
            );
            Facility::create($facility);
        }
        fclose($file);
    }

    public function maintenance()
    {
        return view('error',[
            'title' => 'Under Maintenance'
        ]);
    }

    public function defaultTable()
    {
        Activity::truncate();
        Baby::truncate();
        PatientForm::truncate();
        PregnantForm::truncate();
        Tracking::truncate();

        return redirect('doctor/patient/tsekap');
    }
}
