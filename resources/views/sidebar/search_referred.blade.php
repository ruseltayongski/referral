<?php
    $select = Session::get('referredSelect');
    $fac = \App\Facility::where('id','<>',$user->facility_id)
                ->where('status',1)
                ->select('facility.id','facility.name')
                ->orderBy('name','asc')
                ->get();
    $facility = Session::get('referred_facility');
    $department = Session::get('referred_department');
    $dept = \App\Department::get();
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">
            Filter Results
            <span class="pull-right badge">Result: {{ $data->total() }}</span>
        </h3>
    </div>
    <div class="panel-body">
        <form method="post" action="{{ url('doctor/referred/search') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" name="keyword" id="keyword" value="{{ Session::get('referredKeyword') }}" class="form-control" placeholder="Code, Firstname, Lastname" />
            </div>
            <div class="form-group">
                <input type="text" id="daterange" value="{{ $start.' - '.$end }}" max="{{ date('Y-m-d') }}" name="date" class="form-control" />
            </div>
            <div class="form-group">
                <select class="form-control select2" name="facility" id="facility">
                    <option value="">All Facility</option>
                    @foreach($fac as $f)
                        <option {{ ($facility==$f->id) ? 'selected':'' }} value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="department" id="department">
                    <option value="">All Department</option>
                    @foreach($dept as $d)
                        <option {{ ($department==$d->id) ? 'selected': '' }} value="{{ $d->id }}">{{ $d->description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select name="type" class="form-control" id="option">
                    <option value="">All Transaction</option>
                    <option @if($select=='referred') selected @endif value="referred">Referred</option>
                    <option @if($select=='seen') selected @endif value="seen">Seen</option>
                    <option @if($select=='accepted') selected @endif value="accepted">Accepted</option>
                    <option @if($select=='arrived') selected @endif value="arrived">Arrived</option>
                    <option @if($select=='admitted') selected @endif value="admitted">Admitted</option>
                    <option @if($select=='discharged') selected @endif value="discharged">Discharged</option>
                    <option @if($select=='cancelled') selected @endif value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-flat btn-danger" onclick="clearFieldsSidebar()">
                    <i class="fa fa-eraser"></i> Clear
                </button>
                <button type="submit" class="btn btn-flat btn-success">
                    <i class="fa fa-filter"></i> Filter Result
                </button>
            </div>
        </form>

    </div>
</div>