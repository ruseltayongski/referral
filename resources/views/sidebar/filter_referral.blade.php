<?php
    $user = \Illuminate\Support\Facades\Session::get("auth");
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
                <input type="text" placeholder="Code, Firstname, Lastname" class="form-control" value="{{ $search_referral['keyword'] }}" name="keyword"/>
            </div>
            <div class="form-group">
                <input type="text" id="daterange" value="{{ $start.' - '.$end }}" max="{{ date('Y-m-d') }}" name="date" class="form-control" />
            </div>
            <div class="form-group">
                <select class="form-control" name="department">
                    <option value="">All Department</option>
                    @foreach($dept as $d)
                        <option {{ ($search_referral['department']==$d->id) ? 'selected': '' }} value="{{ $d->id }}">{{ $d->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="option">
                    <option value="">Select All</option>
                    <option {{ ($search_referral['option']=='referred') ? 'selected': '' }} value="referred">New Referral</option>
                    <option {{ ($search_referral['option']=='accepted') ? 'selected': '' }} value="accepted">Accepted</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-flat btn-success">
                    <i class="fa fa-filter"></i> Filter Result
                </button>
            </div>
        </form>
    </div>
</div>