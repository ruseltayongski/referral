<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <style>
        .title-name {
            font-weight:bold;
            font-size:1.2em;
        }
        .widget-user-header {
            padding: 10px 15px !important;
        }
        .widget-user-2 .widget-user-username {
            margin-top: 5px;
            margin-bottom: 0px;
            font-size: 22px;
            font-weight: 300;
        }
    </style>
    <div class="col-md-3">
        @include('sidebar.filter_list')
        @include('sidebar.quick')
    </div>
    <div class="col-md-9">
        <div class="jim-content hide">
            <h3 class="page-header">{{ $title }}</h3>
            <div class="row">
                <div class="col-md-12">

                    @if(count($data) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <tr class="bg-black">
                                <th>NAME</th>
                                <th>FACILITY</th>
                                <th>CONTACT</th>
                                <th>STATUS</th>
                            </tr>
                            @foreach($data as $row)
                            <?php
                                $strMname = strlen($row->mname);
                                $mname = '';
                                if($strMname>0){
                                    $mname = $row->mname[0] .'.';
                                }

                                $name = "$row->fname $mname $row->lname";
                                $status = '<strong>ON DUTY</strong>';
                                $class = 'text-success';
                                if($row->status=='login_off')
                                {
                                    $status = '<em>OFF DUTY</em>';
                                    $class = 'text-danger';
                                }
                            ?>
                            <tr>
                                <td class="title-name {{ $class }}">Dr. {{ ucwords(mb_strtolower($name))}}</td>
                                <td class="text-muted">
                                    <strong>{{ $row->facility }}</strong>
                                    @if($row->department)
                                    <br />
                                    <small class="text-danger"><em>({{ $row->department }})</em></small>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $row->contact }}</td>
                                <td class="text-muted {{ $class }}">
                                    {!! $status !!}
                                    <br />
                                    @if($class=='text-success')
                                    <small class="text-muted">{{ date('h:i A',strtotime($row->login)) }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        <hr />
                    </div>

                    @else
                        <div class="alert-section">
                            <div class="alert alert-warning">
                                <span class="text-warning">
                                    <i class="fa fa-warning"></i> No Online Doctors!
                                </span>
                            </div>
                        </div>

                        <ul class="timeline">
                        </ul>
                    @endif
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

        <div class="jim-content" style="background: rgba(255, 255, 255, 0.4)">
            <h3 class="page-header">{{ $title }}</h3>
                @if(count($data)>0)
                    @foreach($data as $row)
                        <?php
                            $color = 'green';
                            $status = 'ON DUTY';
                            if($row->status=='login_off')
                            {
                                $status = '<em>OFF DUTY</em>';
                                $color = 'yellow';
                            }
                        ?>
                        <div class="col-md-4">
                            <!-- Widget: user widget style 1 -->
                            <div class="box box-widget widget-user-2">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-{{ $color }}-active">
                                    <?php
                                        $fname = ucwords(mb_strtolower($row->fname));
                                        $name = ucwords(mb_strtolower($row->lname))." ".$fname[0];
                                        $facility = $row->facility;
                                    ?>
                                    <h3 class="widget-user-username" style="margin-left: 0px;" title="{{ $name }}">
                                        Dr.
                                        @if(strlen($name)>16)
                                            {{ substr($name,0,16) }}...
                                        @else
                                            {{ $name }}.
                                        @endif
                                    </h3>
                                    <h5 class="widget-user-desc" style="margin-left: 0px;">{{ $row->abbr }}</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="#">{{ $row->contact }} <span class="pull-right badge bg-blue"><i class="fa fa-phone"></i> </span></a></li>
                                        <li><a href="#">{{ $row->department }} <span class="pull-right badge bg-aqua"><i class="fa fa-hospital-o"></i> </span></a></li>
                                        <li><a href="#" class="text-{{ $color }}">{!! $status !!} <span class="pull-right badge bg-{{ $color }}">{{ date('h:i A',strtotime($row->login)) }}</span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.widget-user -->
                        </div>
                    @endforeach
                @else
                    <div class="alert-section">
                        <div class="alert alert-warning">
                                        <span class="text-warning">
                                            <i class="fa fa-warning"></i> No Online Doctors!
                                        </span>
                        </div>
                    </div>

                    <ul class="timeline">
                    </ul>
                @endif
            <div class="clearfix"></div>
        </div>
    </div>
@endsection
@section('js')

@endsection

