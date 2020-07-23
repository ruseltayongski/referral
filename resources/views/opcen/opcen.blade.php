@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-body">
                <div class="col-sm-6 col-xs-12">
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-user-md"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Client</span>
                            <span class="info-box-number countDoctors">1,231</span>
                            <div class="progress">
                                <div class="progress-bar profilePercentageBar"></div>
                            </div>
                            <span class="progress-description"></span>
                        </div><!-- /.info-box-content -->
                    </div>
                </div>

                <div class="col-sm-6 col-xs-12">
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">UNDER DEVELOPMENT</span>
                            <span class="info-box-number countOnline">1</span>
                            <div class="progress">
                                <div class="progress-bar profilePercentageBar"></div>
                            </div>
                            <span class="progress-description">
                  </span>
                        </div><!-- /.info-box-content -->
                    </div>
                </div>

                <div class="col-sm-6 col-xs-12">
                    <div class="info-box bg-red">
                        <span class="info-box-icon"><i class="fa fa-hospital-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">UNDER DEVELOPMENT</span>
                            <span class="info-box-number countFacility">50</span>
                            <div class="progress">
                                <div class="progress-bar profilePercentageBar"></div>
                            </div>
                            <span class="progress-description">
                  </span>
                        </div><!-- /.info-box-content -->
                    </div>
                </div>

                <div class="col-sm-6 col-xs-12">
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-file-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">UNDER DEVELOPMENT</span>
                            <span class="info-box-number countReferral">11,577</span>
                            <div class="progress">
                                <div class="progress-bar profilePercentageBar"></div>
                            </div>
                            <span class="progress-description">
                  </span>
                        </div><!-- /.info-box-content -->
                    </div>
                </div>

            </div>
        </div>
    </div>



@endsection

@section('js')

@endsection

