<?php
$user = \Illuminate\Support\Facades\Session::get('auth');
$fac = \App\Facility::where('id','<>',$user->facility_id)
    ->where('status',1)
    ->select('facility.id','facility.name')
    ->orderBy('name','asc')
    ->get();

$facility = \Illuminate\Support\Facades\Session::get('report_outgoing_facility');

?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Filter Result
            <span class="badge pull-right">Result: {{ $data->total() }}</span>
        </h3>
    </div>
    <div class="panel-body">
        <form method="post" action="{{ url('doctor/report/outgoing') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" id="daterange" max="{{ date('Y-m-d') }}" name="date" class="form-control" />
            </div>
            <div class="form-group">
                <select name="facility" class="form-control">
                    <option value="">All Facility</option>
                    @foreach($fac as $f)
                        <option {{ ($facility==$f->id) ? 'selected':'' }} value="{{ $f->id }}">{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fa fa-filter"></i> Filter Result
                </button>
            </div>
        </form>
    </div>
</div>