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
    </style>
    <div class="col-md-3">
        @include('sidebar.filter_list')
        @include('sidebar.quick')
    </div>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">{{ $title }}
            </h3>
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
                                $name = "$row->fname $row->mname $row->lname";
                                $status = '<strong>ON DUTY</strong>';
                                $class = 'text-success';
                                if($row->login_status=='login_off')
                                {
                                    $status = '<em>OFF DUTY</em>';
                                    $class = 'text-danger';
                                }
                            ?>
                            <tr>
                                <td class="title-name {{ $class }}">Dr. {{ $name }}</td>
                                <td class="text-muted">
                                    <strong>{{ $row->facility }}</strong>
                                    @if($row->department)
                                    <br />
                                    <small class="text-danger"><em>({{ $row->department }})</em></small>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $row->contact }}</td>
                                <td class="text-muted {{ $class }}">{!! $status !!}</td>
                            </tr>
                            @endforeach
                        </table>
                        <hr />
                    </div>
                    <div class="text-center">
                        {{ $data->links() }}
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

    </div>
@endsection
@section('js')

@endsection

