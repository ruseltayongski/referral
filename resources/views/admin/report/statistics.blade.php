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
                            <input type="text" class="form-control" name="date_range" value="{{ date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)) }}" placeholder="Filter your daterange here..." id="consolidate_date_range">
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
                                <th>Incoming</th>
                                <th>Accepted</th>
                                <th>Redirected</th>
                                <th>Seen</th>
                                <th>Seen only</th>
                            </tr>
                            <?php
                            $count = 0;
                            ?>
                            @foreach($data as $row)
                                <?php
                                $count++;

                                ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="5">
                                            <strong class="text-green">{{ $row->province }}</strong>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="">
                                    <td>{{ $count }}</td>
                                    <td >{{ $row->name }}</td>
                                    <td width="10%">{{ $row->incoming }}</td>
                                    <td width="10%">{{ $row->accepted }}</td>
                                    <td width="10%">{{ $row->redirected }}</td>
                                    <td width="10%">{{ $row->seen_only }}</td>
                                    <th>Under Development</th>
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
        $('#consolidate_date_range').daterangepicker();
    </script>
@endsection

