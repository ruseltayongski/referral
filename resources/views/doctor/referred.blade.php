<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <style>
        .timeline .facility {
            color: #ff8456;
        }
    </style>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">Referred Patients
            </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->
                    <ul class="timeline">
                        <!-- timeline time label -->

                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <li>
                            <i class="fa fa-user bg-blue-active"></i>
                            <div class="timeline-item normal-section">
                                <span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> was referred to <span class="facility">Badian District Hospital</span></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                    <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-user bg-blue-active"></i>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> was referred to <span class="facility">Badian District Hospital</span></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                    <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-user-times"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a>  was rejected by <span class="facility">Badian District Hospital</span> and referred to <span class="facility">Toledo District Hospital</span> </h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-info btn-xs" href="#" data-toggle="modal" data-backdrop="static"><i class="fa fa-file-o"></i> View Remarks/Reason</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-user-plus"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a>  was accepted to <span class="facility">Toledo District Hospital</span></h3>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-user bg-blue-active"></i>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> was referred to <span class="facility">Badian District Hospital</span></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                    <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-user-times"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a>  was rejected by <span class="facility">Badian District Hospital</span> and referred to <span class="facility">Toledo District Hospital</span> </h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-info btn-xs" href="#" data-toggle="modal" data-backdrop="static"><i class="fa fa-file-o"></i> View Remarks/Reason</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-user-plus"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a>  was accepted to <span class="facility">Toledo District Hospital</span></h3>
                            </div>
                        </li>

                    </ul>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
    <div class="col-md-3">
        @include('sidebar.quick')
    </div>
    @include('modal.accept_reject')
    @include('modal.reject')
    @include('modal.refer')
    @include('modal.accept')
@endsection
@include('script.firebase')
@section('js')
    <script>
        var dbRef = firebase.database();
        var connRef = dbRef.ref('Referral');
        var myfacility = "{{ $user->facility_id }}";
    </script>
@endsection

