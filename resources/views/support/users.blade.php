<?php
$user = Session::get('auth');
$searchKeyword = Session::get('searchKeyword');
$keyword = '';
if($searchKeyword){
    $keyword = $searchKeyword['keyword'];
}
?>
@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px;
        }
        .form-group {
             margin-bottom: 10px;
        }
    </style>
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <div class="pull-right">
                    <form action="{{ url('support/users/search') }}" method="POST" class="form-inline">
                        {{ csrf_field() }}
                        <div class="form-group-sm" style="margin-bottom: 10px;">
                            <input type="text" class="form-control" name="keyword" placeholder="Search name..." value="{{ $keyword }}">
                            <button type="submit" class="btn btn-success btn-sm btn-flat">
                                <i class="fa fa-search"></i> Search
                            </button>
                            @if($user->facility_id!=25)
                            <a href="#addUserModal" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                                <i class="fa fa-user-plus"></i> Add User
                            </a>
                            @else
                            <a href="{{ url('support/uers/add') }}" class="btn btn-primary btn-sm btn-flat">
                                <i class="fa fa-user-plus"></i> Add User
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
                <h3>{{ $title }}</h3>
            </div>
            <div class="box-body">
                @if(count($data)>0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tr class="bg-black">
                            <th>Name</th>
                            <th>Department</th>
                            <th>Contact</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Last Login</th>
                        </tr>
                        @foreach($data as $row)
                        <tr>
                            <td>
                                <a href="#updateUserModal"
                                   data-toggle="modal"
                                   data-id = "{{ $row->id }}"
                                   class="title-info update_info">
                                Dr. {{ $row->fname }} {{ $row->mname }} {{ $row->lname }}
                                </a>
                            </td>
                            <td>
                                {{ \App\Department::find($row->department_id)->description }}
                            </td>
                            <td>
                                {{ $row->contact }}
                            </td>
                            <td>
                                {{ $row->username }}
                            </td>
                            <td>
                                <span class="{{ ($row->status=='active') ? 'text-success': 'text-danger' }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td class="text-warning">
                                @if($row->last_login!='0000-00-00 00:00:00')
                                {{ date('M d, Y h:i A',strtotime($row->last_login)) }}
                                @else
                                Never Login
                                @endif
                            </td>
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
                            <i class="fa fa-warning"></i> No doctors found!
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('support.modal.addUser')
@endsection
@section('js')
@include('support.script.users')
@endsection

