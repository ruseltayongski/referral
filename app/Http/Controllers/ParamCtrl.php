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

    public static function getLastLogin($facility_id)
    {
        $doctor = User::where('level','doctor')
                ->where('facility_id',$facility_id)
                ->where('last_login','!=','0000-00-00 00:00:00')
                ->orderBy('last_login','desc')
                ->first();
        return $doctor;
    }

    public function admin()
    {
        $data = array(
            'username' => 'admin_doh',
            'password' => bcrypt('s3cur1ty'),
            'level' => 'admin',
            'facility_id' => 63,
            'fname' => 'Admin',
            'mname' => 'RO7',
            'lname' => 'DOH',
            'contact' => '418-7633',
            'email' => 'jimmy.lomocso@gmail.com',
            'muncity' => 63,
            'province' => 2,
            'designation' => 'CP II',
            'status' => 'active'
        );
        User::create($data);
    }

    public function support()
    {
        $data = array(
            'username' => 'IT_VSMMC',
            'password' => bcrypt('IT_VSMMC'),
            'level' => 'support',
            'facility_id' => 24,
            'fname' => 'IT',
            'mname' => 'Support',
            'lname' => 'VSMMC',
            'contact' => 'N/A',
            'email' => 'N/A',
            'muncity' => 63,
            'province' => 2,
            'designation' => 'IT',
            'status' => 'active'
        );
        User::create($data);

        $data = array(
            'username' => 'IT_SAMCH',
            'password' => bcrypt('IT_SAMCH'),
            'level' => 'support',
            'facility_id' => 19,
            'fname' => 'IT',
            'mname' => 'Support',
            'lname' => 'SAMCH',
            'contact' => 'N/A',
            'email' => 'N/A',
            'muncity' => 63,
            'province' => 2,
            'designation' => 'IT',
            'status' => 'active'
        );
        User::create($data);

        $data = array(
            'username' => 'IT_TDH',
            'password' => bcrypt('IT_TDH'),
            'level' => 'support',
            'facility_id' => 23,
            'fname' => 'IT',
            'mname' => 'Support',
            'lname' => 'TDH',
            'contact' => 'N/A',
            'email' => 'N/A',
            'muncity' => 67,
            'province' => 2,
            'designation' => 'IT',
            'status' => 'active'
        );
        User::create($data);

        $data = array(
            'username' => 'IT_ECS',
            'password' => bcrypt('IT_ECS'),
            'level' => 'support',
            'facility_id' => 25,
            'fname' => 'IT',
            'mname' => 'Support',
            'lname' => 'ECS',
            'contact' => 'N/A',
            'email' => 'N/A',
            'muncity' => 80,
            'province' => 2,
            'designation' => 'IT',
            'status' => 'active'
        );
        User::create($data);
    }
}
