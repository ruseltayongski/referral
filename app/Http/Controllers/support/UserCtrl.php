<?php

namespace App\Http\Controllers\support;

use App\Department;
use App\Facility;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('support');
    }

    public function index(Request $request)
    {
        $user = Session::get('auth');

        $search = $request->search;
        $status = $request->status;
        $perPage = $request->get('per_page', 15);

        $data = User::where('facility_id', $user->facility_id)
            ->where(function($q){
                $q->where("users.level","doctor")
                ->orWhere("users.level","midwife")
                ->orWhere("users.level","nurse")
                ->orWhere("users.level","medical_dispatcher");
            })
            ->where(function($q) use($search){
                $q->where('fname','like',"%$search%")
                ->orWhere('mname','like',"%$search%")
                ->orWhere('lname','like',"%$search%")
                ->orWhere('username','like',"%$search%")
                ->orWhere(DB::raw('concat(fname," ",lname)'),'like',"%$search%")
                ->orWhere(DB::raw('concat(lname," ",fname)'),'like',"%$search%");
            });

        // Department Filter
        if($request->department_id){
            $data->where(
                "department_id",
                $request->department_id == 'no_department'
                    ? 0
                    : $request->department_id
            );
        }

        // Status Filter
        if($status){
            $data->where('status', $status);
        }

        $data = $data
            ->orderBy('fname','asc')
            ->paginate($perPage)
            ->appends($request->all());

        $departments = Department::get();

        return view('support.users',[
            'title' => 'Manage Users',
            'data' => $data,
            'departments' => $departments,
            'search' => $search,
            'status' => $status,
            'perPage' => $perPage,
            'group_by_department' => $group_by_department
        ]);
    }

    public function create()
    {
        $departments = Department::get();
        return view('support.addUser',[
            'title' => 'Add User',
            'departments' => $departments
        ]);
    }

    public function store(Request $req)
    {
    
        $otherDepartments = implode(',', $req->input('other_department_id', []));
        $user = Session::get('auth');
        $match = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname
        );

        $facility = Facility::find($req->facility_id);

        $data = array(
            'level' => $req->level,
            'facility_id' => $user->facility_id,
            'status' => 'active',
            'contact' => $req->contact,
            'email' => $req->email,
            'designation' => $req->designation,
            'department_id' => $req->department_id,
            'other_department_telemed' => $otherDepartments,
            'subopd_id' => $req->opdSub_id ?? $req->AddopdSub_id,
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'muncity' => $facility->muncity,
            'province' => $facility->province,
            'created_by' => $user->id
        );
        User::updateOrCreate($match,$data);
        return 'added';
    }

    public function add(Request $req)
    {

        $user = Session::get('auth');
        $match = array(
            'fname' => $req->fname,
            'mname' => ($req->mname) ? $req->mname: '',
            'lname' => $req->lname
        );
        $email = 'N/A';
        if($req->email)
        {
            $email = $req->email;
        }

        $facility = Facility::find($user->facility_id);
        $data = array(
            'level' => $req->level,
            'facility_id' => $user->facility_id,
            'status' => 'active',
            'contact' => $req->contact,
            'email' => $email,
            'designation' => $req->designation,
            'department_id' => $req->department_id,
            'subopd_id' => $req->opdSub_id ?? $req->AddopdSub_id ?? '',
            'username' => $req->username,
            'password' => bcrypt($req->password),
            'muncity' => $facility->muncity,
            'province' => $facility->province
        );
        User::updateOrCreate($match,$data);

        return redirect()->back()->with('status','added');
    }

    public function update(Request $req)
    {
        $edit_other_departments = $req->input('edit_other_department_id');
        // Log::info('Showing the user profile for user: update', ['users' => $req->all()]);

        $other_department_telemed = implode(',', $edit_other_departments);

        $subopd_id = '';

        if ($req->filled('AddeditopdSub_id')) {
            $subopd_id = $req->AddeditopdSub_id;
        } elseif ($req->filled('editopdSub_id')) {
            $subopd_id = $req->editopdSub_id;
        }

        $user = Session::get('auth');
        $facility = Facility::find($user->facility_id);
        $data = array(
            'fname' => $req->fname,
            'mname' => $req->mname,
            'lname' => $req->lname,
            'level' => $req->level,
            'contact' => $req->contact,
            'email' => ($req->email) ? $req->email: 'N/A',
            'designation' => $req->designation,
            'department_id' => $req->department_id,
            'other_department_telemed' => $other_department_telemed, 
            'subopd_id' => $subopd_id,
            'username' => $req->username,
            'status' => $req->status,
            'muncity' => $facility->muncity,
            'province' => $facility->province
        );

        if ($req->password)
        {
            $data['password'] = bcrypt($req->password);
        }


        User::where('id',$req->user_id)
                ->update($data);
        return 'updated';
    }

    public function check($string)
    {
        $user = Session::get('auth');
        $check = User::where('username',$string)
                ->where('id','!=',$user->id)
                ->first();
        if($check){
            return '1';
        }
        return '0';
    }

    public function checkUpdate($string,$user_id)
    {
        $user = Session::get('auth');
        $check = User::where('username',$string)
                ->where('id','!=',$user->id)
                ->where('id','!=',$user_id)
                ->first();
        if($check){
            return '1';
        }
        return '0';
    }

    public function info($user_id)
    {
        return User::find($user_id);
    }

    public function ajaxSearch(Request $request)
    {
        $user = Session::get('auth');

        $search = $request->search ?? '';
        $status = $request->status ?? '';
        $perPage = $request->get('per_page', 15);

        $data = User::where('facility_id', $user->facility_id)
            ->where(function($q){
                $q->where("users.level","doctor")
                ->orWhere("users.level","midwife")
                ->orWhere("users.level","nurse")
                ->orWhere("users.level","medical_dispatcher");
            })
            ->where(function($q) use($search){
                if($search) {
                    $q->where('fname','like',"%$search%")
                    ->orWhere('mname','like',"%$search%")
                    ->orWhere('lname','like',"%$search%")
                    ->orWhere('username','like',"%$search%")
                    ->orWhere(DB::raw('concat(fname," ",lname)'),'like',"%$search%")
                    ->orWhere(DB::raw('concat(lname," ",fname)'),'like',"%$search%");
                }
            });

        // Status Filter
        if($status){
            $data->where('status', $status);
        }

        $data = $data
            ->orderBy('fname','asc')
            ->paginate($perPage);

        $html = '';

        if($data->count() > 0) {
            foreach($data as $row) {
                $abbreviation = '';
                if($row->level == 'doctor') {
                    $abbreviation = "Dr. ";
                }

                $department = Department::find($row->department_id);
                $departmentDescription = '';
                if($department) {
                    $departmentDescription = $department->description;
                }

                $lastLogin = '';
                if($row->last_login != '0000-00-00 00:00:00') {
                    $lastLogin = date('M d, Y h:i A', strtotime($row->last_login));
                } else {
                    $lastLogin = 'Never Login';
                }

                $statusClass = ($row->status == 'active') ? 'text-success' : 'text-danger';

                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<a href="#updateUserModal" data-toggle="modal" data-id="'.$row->id.'" class="title-info update_info">';
                $html .= $abbreviation . $row->fname . ' ' . $row->mname . ' ' . $row->lname;
                $html .= '</a>';
                $html .= '</td>';
                $html .= '<td>' . $departmentDescription . '</td>';
                $html .= '<td>' . $row->contact . '</td>';
                $html .= '<td>' . $row->username . '</td>';
                $html .= '<td><span class="' . $statusClass . '">' . strtoupper($row->status) . '</span></td>';
                $html .= '<td class="text-warning">' . $lastLogin . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="6" style="text-align:center; color:#999;"><em>No users found</em></td>';
            $html .= '</tr>';
        }

        return $html;
    }
}
