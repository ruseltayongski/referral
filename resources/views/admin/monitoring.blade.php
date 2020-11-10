@extends('layouts.app')

@section('content')
    <style>
        .editable-empty{
            color: #ff3d3c;
        }
    </style>
    <div class="box box-success">
        <div class="box-body">
            <div class="box-header with-border">
                <h3>
                    Referral that not accepted within 30 minutes as {{ date("F d,Y g:i a") }}
                </h3>
                <form action="{{ asset('monitoring') }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group-sm">
                        <input type="text" class="form-control active" name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}" placeholder="Filter your daterange here..." id="consolidate_date_range" autocomplete="off">
                        <button type="submit" class="btn-sm btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Referring Facility</th>
                        <th>Referred To</th>
                        <th>Date Referred</th>
                        <th>Turn around time not accepted</th>
                        <th>Remarks</th>
                    </tr>
                    <?php $count=0; ?>
                    @foreach($pending_activity as $row)
                        <?php $count++; ?>
                        <tr>
                            <td width="2%"><b class="text-yellow">{{ $count }}</b></td>
                            <td width="5%">
                                <a href="{{ asset('doctor/track/patient?referredCode=').$row->code }}" class="btn btn-success" target="_blank">
                                    <i class="fa fa-stethoscope"></i> Track
                                </a>
                            </td>
                            <td width="25%;">
                                {{ $row->referring_facility }}<br>
                                <b class="text-green">{{ $row->contact_from }}</b>
                            </td>
                            <td width="25%;">
                                {{ $row->referred_to }}<br>
                                <b class="text-green">{{ $row->contact_to }}</b>
                            </td>
                            <td>{{ date("F d,Y g:i a",strtotime($row->date_referred)) }}</td>
                            <td>{{ $row->time_not_accepted }} minutes</td>
                            <td><span class="badge bg-red">Under Development</span></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>


@endsection

@section('js')
    <script>
        //Date range picker
        $('#consolidate_date_range').daterangepicker();
    </script>
@endsection

