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
                                <th>Patient Code</th>
                                <th>Patient Name</th>
                                <th>Date Accepted</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $row)
                            <tr>
                                <td><span class="facility">{{ $row->name }}</span></td>
                                <td>{{ $row->code }}</td>
                                <td>{{ $row->patient_name }}</td>
                                <td>{{ $row->date_accepted }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-action btn-arrive"
                                        data-track_id="{{ $row->id }}"
                                        data-patient_name="{{ $row->patient_name }}"
                                        data-code="{{ $row->code}}">
                                        <i class="fa fa-wheelchair"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info btn-action btn-admit"
                                            data-track_id="{{ $row->id }}"
                                            data-patient_name="{{ $row->patient_name }}"
                                            data-code="{{ $row->code}}">
                                        <i class="fa fa-stethoscope"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning btn-action btn-discharge"
                                            data-track_id="{{ $row->id }}"
                                            data-patient_name="{{ $row->patient_name }}"
                                            data-code="{{ $row->code}}">
                                        <i class="fa fa-wheelchair-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success btn-action btn-transfer"
                                            data-toggle="modal"
                                            data-target="#referFormModal"
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
                    </div>
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
@endsection
@include('script.firebase')
@section('js')
@include('script.accepted')
@endsection

