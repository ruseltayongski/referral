@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <div class="pull-right">
                    <form action="{{ asset('admin/facility') }}" method="POST" class="form-inline">
                        {{ csrf_field() }}
                        <div class="form-group-sm" style="margin-bottom: 10px;">
                            <input type="text" class="form-control" name="keyword" placeholder="Search inventory..." value="{{ Session::get("keyword") }}">
                            <button type="submit" class="btn btn-success btn-sm btn-flat">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="submit" value="view_all" name="view_all" class="btn btn-warning btn-sm btn-flat">
                                <i class="fa fa-eye"></i> View All
                            </button>
                            <!--
                            <a href="#bed_modal" data-toggle="modal" class="btn btn-info btn-sm btn-flat">
                                <i class="fa fa-hospital-o"></i> Add Inventory
                            </a>
                            -->
                        </div>
                    </form>
                </div>
                <h3 class="text-yellow">{{ \App\Facility::find($facility_id)->name }}</h3>
            </div>
            <div class="box-body">
                @if(count($inventory) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tr class="bg-black">
                                <th></th>
                                <th>Description</th>
                                <th>Capacity</th>
                                <th>Occupied</th>
                                <th>Available</th>
                            </tr>
                            <?php $count=0; ?>
                            @foreach($inventory as $row)
                                <?php $count++; ?>
                                <tr >
                                    <td>{{ $count }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td><a href="#" id="capacity">{{ $row->capacity }}</a></td>
                                    <td><a href="#" id="occupied">{{ $row->occupied }}</a></td>
                                    <td>{{ $row->available }}</td>
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

@section('js')
    <script>
        @if(Session::get('bed'))
        Lobibox.notify('success', {
            title: "",
            msg: "Successfully added bed!",
            size: 'mini',
            rounded: true
        });
        <?php Session::put("bed",false); ?>
        @endif
    </script>

    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

    <script>
        $.fn.editable.defaults.mode = 'popup';
        $(document).ready(function() {
            $('#capacity').editable();
            $('#occupied').editable();
        });
    </script>
@endsection

