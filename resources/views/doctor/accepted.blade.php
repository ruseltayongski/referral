<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <style>
        .facility {
            color: #ff8456;
        }
    </style>
    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">{{ $title }}
            </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->
                    @if(count($data)>0)
                    <div class="hide info alert alert-success">
                        <span class="text-success">
                            <i class="fa fa-check"></i> <span class="message"></span>
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="bg-gray">
                            <tr>
                                <th>Referring Facility</th>
                                <th>Patient Name/Code</th>
                                <th>Date Accepted</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $row)
                            <?php
                                $modal = ($row->type=='normal') ? '#normalFormModal' : '#pregnantFormModal';
                                $type = ($row->type=='normal') ? 'Non-Pregnant' : 'Pregnant';
                            ?>
                            <tr>
                                <td>
                                    <span class="facility">{{ $row->name }}</span>
                                    <br />
                                    <span class="text-muted">{{ $type }}</span>
                                </td>
                                <td>
                                    <a href="{{ $modal }}" class="view_form"
                                       data-toggle="modal"
                                       data-type="{{ $row->type }}"
                                       data-id="{{ $row->id }}"
                                       data-code="{{ $row->code }}">
                                        <span class="text-primary">{{ $row->patient_name }}</span>
                                        <br />
                                        <small class="text-warning">{{ $row->code }}</small>
                                    </a>
                                </td>
                                <td>{{ $row->date_accepted }}</td>
                                <?php
                                    $status = '';
                                    $current = \App\Activity::where('code',$row->code)
                                        ->orderBy('id','desc')
                                        ->first();
                                    if($current)
                                    {
                                        $status = strtoupper($current->status);
                                    }
                                ?>
                                <td class="activity_{{ $row->code }}">{{ $status }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-action"
                                        title="Patient Arrived"

                                        data-toggle="modal"
                                        data-toggle="tooltip"
                                        data-target="#arriveModal"
                                        data-track_id="{{ $row->id }}"
                                        data-patient_name="{{ $row->patient_name }}"
                                        data-code="{{ $row->code}}">
                                        <i class="fa fa-wheelchair"></i>
                                    </button>

                                    <button class="btn btn-sm btn-info btn-action"
                                            title="Patient Admitted"

                                            data-toggle="modal"
                                            data-toggle="tooltip"
                                            data-target="#admitModal"
                                            data-track_id="{{ $row->id }}"
                                            data-patient_name="{{ $row->patient_name }}"
                                            data-code="{{ $row->code}}">
                                        <i class="fa fa-stethoscope"></i>
                                    </button>

                                    <button class="btn btn-sm btn-warning btn-action"
                                            title="Patient Discharged"

                                            data-toggle="modal"
                                            data-toggle="tooltip"
                                            data-target="#dischargeModal"
                                            data-track_id="{{ $row->id }}"
                                            data-patient_name="{{ $row->patient_name }}"
                                            data-code="{{ $row->code}}">
                                        <i class="fa fa-wheelchair-alt"></i>
                                    </button>

                                    <button class="btn btn-sm btn-success btn-action btn-transfer"
                                            title="Refer Patient"

                                            data-toggle="modal"
                                            data-toggle="tooltip"
                                            data-target="#referAcceptFormModal"
                                            data-track_id="{{ $row->id }}"
                                            data-patient_name="{{ $row->patient_name }}"
                                            data-code="{{ $row->code}}">
                                        <i class="fa fa-ambulance"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="text-center">
                            {{ $data->links() }}
                        </div>
                    </div>
                    <table class="table table-striped">
                        <caption>LEGENDS:</caption>
                        <tr>
                            <td class="text-right" width="60px"><button class="btn btn-sm btn-primary"><i class="fa fa-wheelchair"></i></button></td>
                            <td>Patient Arrived</td>
                        </tr>
                        <tr>
                            <td class="text-right" width="60px"><button class="btn btn-sm btn-info"><i class="fa fa-stethoscope"></i> </button></td>
                            <td>Patient Admitted</td>
                        </tr>
                        <tr>
                            <td class="text-right" width="60px"><button class="btn btn-sm btn-warning"><i class="fa fa-wheelchair-alt"></i> </button></td>
                            <td>Patient Discharged</td>
                        </tr>
                        <tr>
                            <td class="text-right" width="60px"><button class="btn btn-sm btn-success"><i class="fa fa-ambulance"></i></button></td>
                            <td>Refer Patient</td>
                        </tr>
                    </table>
                    @else
                    <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No accepted patients!
                        </span>
                    </div>
                    @endif
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
@include('modal.refer')
@include('modal.accepted')
@include('modal.view_form')
@endsection
@include('script.firebase')
@section('js')
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@include('script.datetime')
@include('script.accepted')
@endsection

