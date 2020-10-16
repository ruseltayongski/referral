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
                <h3>Referral that not accepted within 30 minutes</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr class="bg-black">
                        <th>Referral Code</th>
                        <th>Referring Facility</th>
                        <th>Date Referred</th>
                        <th>Turn around time not accepted</th>
                        <th>Remarks</th>
                    </tr>
                    @foreach($pending_activity as $row)
                        <tr>
                            <td width="15%">{{ $row->code }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ date("F d,Y H:i:s",strtotime($row->date_referred)) }}</td>
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
        /*$("#container").removeClass("container");
        $("#container").addClass("container-fluid");*/
    </script>
@endsection

