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
            <h3 class="page-header">Incoming Patients
            </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->
                    <ul class="timeline">
                        <!-- timeline time label -->

                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        <li>
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item normal-section">
                                <span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> is referred to your hospital from <span class="facility">Cebu Health Unit</span></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                    <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item pregnant-section">
                                <span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> is referred to your hospital from <span class="facility">Cebu Health Unit</span></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                    <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item read-section">
                                <span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> is referred to your hospital from <span class="facility">Cebu Health Unit</span></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                    <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <i class="fa fa-user-plus bg-olive"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> March 6, 2018 01:23 PM</span>
                                <h3 class="timeline-header no-border"><a href="#">Ester Forester</a> was ACCEPTED</h3>

                            </div>

                        </li>
                        <li>
                            <i class="fa fa-user-times bg-maroon"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> March 5, 2018 03:45 PM</span>
                                <h3 class="timeline-header no-border"><a href="#">Garry Harlem</a> was REJECTED</h3>

                            </div>

                        </li>
                        <!-- END timeline item -->
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
    var count_referral = $('.count_referral').html();
    var content = '';

    console.log(myfacility);
    connRef.child(myfacility).on('child_added',function(snapshot){
        var data = snapshot.val();
        count_referral = parseInt(count_referral);
        count_referral += 1;
        $('.count_referral').html(count_referral);
        content = '<li>' +
            '                            <i class="fa fa-ambulance bg-blue-active"></i>' +
            '                            <div class="timeline-item unread-section">' +
            '                                <span class="time"><i class="fa fa-calendar"></i> '+data.date+'</span>' +
            '                                <h3 class="timeline-header no-border"><a href="#">'+data.name+'</a> is referred to your facility from <a href="#">'+data.referring_name+'</a></h3>' +
            '                                <div class="timeline-footer">\n' +
            '                                    <a class="btn btn-info btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>' +
            '                                </div>' +
            '                            </div>' +
            '                        </li>';
        $('.timeline').prepend(content);

        console.log(data);
    });
</script>
@endsection

