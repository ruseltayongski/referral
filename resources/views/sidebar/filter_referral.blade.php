<?php
    $user = \Illuminate\Support\Facades\Session::get("auth");
    $fac = \App\Facility::where('id','<>',$user->facility_id)
        ->where('status',1)
        ->select('facility.id','facility.name')
        ->orderBy('name','asc')
        ->get();
    $dept = \App\Department::leftJoin('users','users.department_id','=','department.id')
                ->select('department.*')
                ->where('users.department_id','<>','')
                ->where('users.facility_id',$user->facility_id)
                ->distinct('department.id')
                ->get();
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">
            Filter Results
            <span class="pull-right badge">Result: {{ $data->total() }}</span>
        </h3>
    </div>
    <div class="panel-body">
        <form action="{{ url('doctor/referral/') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" placeholder="Code, Firstname, Lastname" class="form-control" value="{{ $search_referral['keyword'] }}" id="keyword" name="keyword"/>
            </div>
            <div class="form-group">
                <input type="text" id="daterange" value="{{ $start.' - '.$end }}" max="{{ date('Y-m-d') }}" name="date" class="form-control" />
            </div>
            <div class="form-group">
                <select class="form-control select2" name="facility" id="facility">
                    <option value="">All Facility</option>
                    @foreach($fac as $f)
                        <option {{ ($search_referral['facility']==$f->id) ? 'selected':'' }} value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="department" id="department">
                    <option value="">All Department</option>
                    @foreach($dept as $d)
                        <option {{ ($search_referral['department']==$d->id) ? 'selected': '' }} value="{{ $d->id }}">{{ $d->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="option" id="option">
                    <option value="">Select All</option>
                    <option {{ ($search_referral['option']=='referred') ? 'selected': '' }} value="referred">New Referral</option>
                    <option {{ ($search_referral['option']=='accepted') ? 'selected': '' }} value="accepted">Accepted</option>
                </select>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-flat btn-warning" onclick="clearFieldsSidebar()">
                    <i class="fa fa-eye"></i> View All
                </button>
                <button type="submit" class="btn btn-flat btn-success">
                    <i class="fa fa-filter"></i> Filter Result
                </button>
            </div>
        </form>
    </div>
</div>