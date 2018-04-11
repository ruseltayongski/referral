<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')

    <div class="col-md-9">
        <div class="box box-success">
            <div class="box-header with-border">
                <div class="pull-right">
                    <form action="{{ url('support/users/search') }}" method="POST" class="form-inline">
                        {{ csrf_field() }}
                        <div class="form-group-sm" style="margin-bottom: 10px;">
                            <input type="text" class="form-control" name="player" placeholder="Search name..." value="{{ Session::get('keyword') }}">
                            <button type="submit" class="btn btn-success btn-sm btn-flat">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <a href="#" class="btn btn-primary btn-sm btn-flat">
                                <i class="fa fa-user-plus"></i> Add User
                            </a>
                        </div>

                    </form>
                </div>
                <h3>Players</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr class="bg-black">
                            <th>Name</th>
                            <th>Department</th>
                            <th>POS</th>
                            <th>AGE</th>
                            <th>HT</th>
                            <th>WT</th>
                            <th>SECTION</th>
                        </tr>

                    </table>
                    <div class="text-center">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        @include('support.sidebar.quick')
    </div>
@endsection
@section('js')

@endsection

