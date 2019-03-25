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
                                    <th>Date Discharged</th>
                                    <th>Current Status</th>
                                    <th>History</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $row)
                                    <?php
                                    $modal = ($row->type=='normal') ? '#normalFormModal' : '#pregnantFormModal';
                                    $type = ($row->type=='normal') ? 'Non-Pregnant' : 'Pregnant';
                                    ?>
                                    <tr>
                                        <td style="white-space: nowrap;">
                                    <span class="facility" title="{{ $row->name }}">
                                    @if(strlen($row->name)>25)
                                            {{ substr($row->name,0,25) }}...
                                        @else
                                            {{ $row->name }}
                                        @endif
                                    </span>
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
                                        <td>{{ \App\Http\Controllers\doctor\PatientCtrl::getDischargeDate($current->status,$row->code) }}</td>
                                        <td class="activity_{{ $row->code }}">{{ $status }}</td>
                                        <td>
                                            <a href="#view_history" class="btn btn-block btn-success">
                                                <i class="fa fa-stethoscope"></i> View History
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $data->links() }}
                            </div>
                        </div>

                    @else
                        <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No discharged/transferred patients!
                        </span>
                        </div>
                    @endif
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
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

