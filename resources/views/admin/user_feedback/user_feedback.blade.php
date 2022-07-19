<?php
$counter = 1;
?>

@extends('layouts.app')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <form action="{{ asset('admin/user_feedback') }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group-lg" style="margin-bottom: 10px;">
                        <input type="text" class="form-control" name="keyword"  placeholder="Search...">
                        <button type="submit" class="btn btn-success btn-sm btn-flat">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <button type="submit" value="view_all" name="view_all" class="btn btn-warning btn-sm btn-flat">
                            <i class="fa fa-eye"></i> View All
                        </button>
                    </div>
                </form>
            </div>
            <h3>User Feedback</h3>
        </div>
        <div class="box-body">
            @if(count($data)>0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tr class="bg-olive-active">
                            <th class="text-center"> Name / Facility Name</th>
                            <th class="text-center"> Email </th>
                            <th class="text-center"> &emsp;Subject&emsp;</th>
                            <th class="text-center"> Feedback </th>
                        </tr>
                        @foreach($data as $row)
                            <tr>
                                <td><b> {{ $row->name }} </b></td>
                                <td> {{ $row->email }} </td>
                                <td class="text-center"> {{ $row->subject }} </td>
                                <td> {{ $row->message }} </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="text-center">
                        {{ $data->links() }}
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <span class="text-warning">
                        <i class="fa fa-warning"></i> No user feedback found!
                    </span>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script>

    </script>
@endsection
