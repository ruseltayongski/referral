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
                <!-- <form action="{{ url('support/users') }}" method="GET" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group-sm" style="margin-bottom: 10px;">
                        <input type="text" style="width: 40%;" class="form-control" placeholder="Search name..." name="search" value="{{ $search }}">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="#filter_user" data-toggle="modal" class="btn btn-info btn-sm">
                            <i class="fa fa-filter"></i> Filter
                        </a>
                        <button type="button" class="btn btn-sm btn-warning" onclick="refreshPage()"><i class="fa fa-eye"></i> View All</button>
                        <a href="#addUserModal" data-toggle="modal" class="btn btn-primary btn-sm ">
                            <i class="fa fa-user-plus"></i> Add User  
                        </a>
                    </div>
                </form> -->
                <form action="{{ url('support/users') }}" method="GET" class="form-inline">

                    <input type="text"
                        id="searchInput"
                        class="form-control"
                        style="width:25%;"
                        placeholder="Search name..."
                        name="search"
                        value="{{ request('search') }}">

                    <!-- Status Filter -->
                    <select name="status" class="form-control" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active"
                            {{ request('status') == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive"
                            {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>

                    <!-- Per Page -->
                    <select name="per_page" class="form-control" id="perPageFilter">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000</option>
                    </select>

                    <button type="submit" class="btn btn-success btn-sm" style="display:none;">
                        <i class="fa fa-search"></i> Search
                    </button>

                    <button type="button"
                            class="btn btn-warning btn-sm"
                            onclick="resetFilters()">
                        <i class="fa fa-eye"></i> Reset Filters
                    </button>
                        <a href="#addUserModal" data-toggle="modal" class="btn btn-primary btn-sm ">
                            <i class="fa fa-user-plus"></i> Add User  
                        </a>

                </form>
                <div class="modal fade" role="dialog" id="filter_user">
                    <div class="modal-dialog modal-sm" role="document">
                        <form method="GET" action="{{ url('support/users') }}">
                            {{ csrf_field() }}
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Department:</label>
                                        <select class="form-control" name="department_id" required>
                                            <option value="">Select Department...</option>
                                            @foreach($group_by_department as $row)
                                                <option value="{{ $row->department_id }}">{{ $row->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-info"></i> Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <h3>
                    {{ $title }}
                    @foreach($group_by_department as $row)
                        <span class="badge bg-blue"> {{ $row->y }}</span> <span style="font-size: 8pt;">{{ $row->label }}</span>
                    @endforeach
                </h3>
            </div>
            <div class="box-body">
                @if(count($data)>0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr class="bg-black">
                                <th>Name</th>
                                <th>Department</th>
                                <th>Contact</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Last Login</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @foreach($data as $row)
                            <tr>
                                <td>
                                    <a href="#updateUserModal"
                                       data-toggle="modal"
                                       data-id = "{{ $row->id }}"
                                       class="title-info update_info">
                                    <?php
                                        if($row->level == 'doctor')
                                            $abre = "Dr. ";
                                        else
                                            $abre = "";

                                        echo $abre.$row->fname.' '.$row->mname.' '.$row->lname;
                                    ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        $department = \App\Department::find($row->department_id);
                                        $description = '';
                                        if($department){
                                            $description = $department->description;
                                        }
                                    ?>
                                    {{ $description }}
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
                        </tbody>
                    </table>
                    <div id="searchLoading" style="display:none; text-align:center; padding:10px;">
                        <i class="fa fa-spinner fa-spin"></i> Searching...
                    </div>
                    <div class="pagination">
                        {{ $data->appends(Request::except('page'))->links() }}
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

