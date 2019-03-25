<?php
    $select = \Illuminate\Support\Facades\Session::get('referredSelect');
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Search Referred Patients</h3>
    </div>
    <div class="panel-body">
        <form method="post" action="{{ url('doctor/referred/search') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" name="keyword" value="{{ \Illuminate\Support\Facades\Session::get('referredKeyword') }}" class="form-control" placeholder="Code, Firstname, Lastname" />
            </div>
            <div class="form-group">
                <select name="type" class="form-control">
                    <option value="">All</option>
                    <option @if($select=='referred') selected @endif value="referred">Referred</option>
                    <option @if($select=='seen') selected @endif value="seen">Seen</option>
                    <option @if($select=='accepted') selected @endif value="accepted">Accepted</option>
                    <option @if($select=='arrived') selected @endif value="arrived">Arrived</option>
                    <option @if($select=='admitted') selected @endif value="admitted">Admitted</option>
                    <option @if($select=='discharged') selected @endif value="discharged">Discharged</option>
                    <option @if($select=='cancelled') selected @endif value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-block">
                    <i class="fa fa-search"></i> Search
                </button>
            </div>
        </form>

    </div>
</div>