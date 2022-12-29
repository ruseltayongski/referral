<?php
$user = Session::get('auth');
$counter = 0;
$faci_count = 0;
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
            <h3>Doctors' Assigned Facility</h3>
            <form action="{{ url('admin/doctor/assignment') }}" method="GET">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search_assign" placeholder="Search name..." value="{{ $keyword }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success btn-sm btn-flat">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            @if(count($data)>0)
                {{ $data[0] }}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr class="bg-black">
                            <th>Name</th>
                            <th>Facility</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Last Login</th>
                        </tr>
                        <?php
                            $bg = '';
                        ?>
                        @foreach($data as $row)
                            <tr style="background-color: {{ $bg }};">
                                <td style="width: 30%;">
                                    <a href="#doctor_modal"
                                       onclick="openModal('<?php echo $row['user_id'];?>', '<?php echo $row['username'];?>')"
                                       data-toggle="modal"
                                       class="title-info update_info">
                                        {{ $row['lname'] }}, {{ $row['fname'] }} {{ $row['mname'] }}
                                    </a>
                                </td>
                                <td>
                                    @foreach($row['facilities'] as $faci)
                                        <b style="font-size: 11pt">{{ $faci->faci_name }}</b> <br>
                                        <i>
                                            <span class="text-warning">
                                                {{ $faci->contact }}, {{ $faci->email }}
                                            </span>
                                        </i>
                                        <div class="clearfix"><br><br></div>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @foreach($row['facilities'] as $faci)
                                        {{ ucfirst($faci->status) }}
                                        <div class="clearfix"><br><br></div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($row['facilities'] as $faci)
                                        @if($faci['last_login']!='0000-00-00 00:00:00')
                                            {{ date('M d, Y h:i A',strtotime($faci->last_login)) }}
                                        @else
                                            Never Login
                                        @endif
                                        <div class="clearfix"><br><br></div>
                                    @endforeach
                                </td>
                            </tr>
                            <?php
                                if($bg == '')
                                    $bg = '#F2F2F2';
                                else if($bg == '#F2F2F2')
                                    $bg = '';
                            ?>
                        @endforeach
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <span class="text-warning">
                        <i class="fa fa-warning"></i> No data found! Please search for doctor's name.
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" role="dialog" id="doctor_modal">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="assignDoctor">
            {{ csrf_field() }}
            <input type="hidden" name="user_id" value="">
            <input type="hidden" name="username" value="">
            <div class="modal-content">
                <div class="modal-body"></div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('js')
    <script>
        function openModal(user_id, username) {
            var url = "<?php echo e(url('admin/doctor/assignment/info')); ?>";
            var json = {
                "_token" : "<?php echo csrf_token(); ?>",
                "user_id" : user_id,
                "username" : username
            };
            $.post(url,json, function(result){
                console.log(result);
                $('.modal-body').html(result);
            });
        }
    </script>
@endsection

