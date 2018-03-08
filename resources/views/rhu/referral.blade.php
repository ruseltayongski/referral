@extends('layouts.app')

@section('content')
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
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item unread-section">
                                <span class="time"><i class="fa fa-calendar"></i> March 7, 2018 9:34 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> is referred to your hospital from <a href="#">Cebu Health Unit</a></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-info btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                </div>
                            </div>

                        </li>
                        <li>
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item unread-section">
                                <span class="time"><i class="fa fa-calendar"></i> March 7, 2018 11:40 AM</span>
                                <h3 class="timeline-header no-border"><a href="#">Carlos Dumaguete</a> is referred to your hospital from <a href="#">Cebu Health Unit</a></h3>
                                <div class="timeline-footer">
                                    <a class="btn btn-info btn-xs" href="#normalFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>
                                </div>
                            </div>

                        </li>
                        <li>
                            <i class="fa fa-user-plus bg-olive"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> March 6, 2018 01:23 PM</span>
                                <h3 class="timeline-header no-border"><a href="#">Ester Forester</a> was accepted by your <a href="#">Hospital</a></h3>

                            </div>

                        </li>
                        <li>
                            <i class="fa fa-user-times bg-maroon"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> March 5, 2018 03:45 PM</span>
                                <h3 class="timeline-header no-border"><a href="#">Garry Harlem</a> was rejected by your <a href="#">Hospital</a></h3>

                            </div>

                        </li>
                        <!-- END timeline item -->
                    </ul>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
    <div class="col-md-3">
        @include('sidebar.rhu')
    </div>
    @include('modal.accept_reject')
    @include('modal.reject')
    @include('modal.refer')
    @include('modal.accept')
@endsection

@section('js')

@endsection

