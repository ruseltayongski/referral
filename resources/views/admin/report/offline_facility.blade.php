@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px !important;
        }
    </style>
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <div class="pull-right">
                    <form action="{{ asset('offline/facility') }}" method="POST" class="form-inline">
                        {{ csrf_field() }}
                        <div class="form-group-sm">
                            <input type="text" class="form-control" name="day_date" value="{{ date('m/d/Y',strtotime($day_date)) }}" placeholder="Filter your date here..." id="onboard_picker">
                            <button type="submit" class="btn-sm btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </form>
                </div>
                <h3>{{ $title }}</h3>
            </div>
            <div class="box-body">
                @if(count($data) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tr class="bg-black">
                                <th></th>
                                <th>Facility Name</th>
                                <th>Chief Hospital</th>
                                <th>Contact No</th>
                                <th>Hospital Type</th>
                            </tr>
                            <?php
                            $count = 0;
                            $facility_onboard[1] = 0;
                            $facility_total[1] = 0;
                            $facility_onboard[2] = 0;
                            $facility_total[2] = 0;
                            $province = [];
                            ?>
                            @foreach($data as $row)
                                <?php
                                $count++;

                                if($row->status == 'onboard')
                                    $facility_onboard[$row->province_id]++;

                                $facility_total[$row->province_id]++;
                                ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="5">
                                            <strong class="text-green">{{ $row->province }}</strong>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="@if($row->status == 'onboard'){{ 'bg-yellow' }}@endif">
                                    <td>{{ $count }}</td>
                                    <td class="@if($row->transaction == 'no_transaction' && $row->status == 'onboard'){{ 'bg-red' }}@endif">{{ $row->name }}</td>
                                    <td>{{ $row->chief_hospital }}</td>
                                    <td width="10%">{{ $row->contact }}</td>
                                    <td>
                                        <span class="
                                            <?php
                                                if($row->hospital_type == 'government'){
                                                    echo 'badge bg-green';
                                                }
                                                elseif($row->hospital_type == 'private'){
                                                    echo 'badge bg-blue';
                                                }
                                                elseif($row->hospital_type == 'RHU'){
                                                    echo 'badge bg-yellow';
                                                }
                                                ?>
                                        ">{{ ucfirst($row->hospital_type) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No data found!
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@section('css')

@endsection

@section('js')
    <script>
        //Date range picker
        $('#onboard_picker').daterangepicker({
            "singleDatePicker": true
        });
    </script>
@endsection

