@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3>Facility List</h3>
            </div>
            <div class="box-body">
                @if(count($data) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tr class="bg-black">
                                <th></th>
                                <th>Facility Name</th>
                                <th>Level</th>
                                <th>Hospital Type</th>
                                <th>Option</th>
                            </tr>
                            <?php
                            $count = 0;
                            $province = [];
                            ?>
                            @foreach($data as $row)
                                <?php $count++; ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="6"><strong class="text-green">{{ $row->province.' Province' }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ $count }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->level }}</td>
                                    <td>{{ ucfirst($row->hospital_type) }}</td>
                                    <td width="7%"><a href="{{ asset('inventory').'/'.$row->id }}" class="btn-xs btn-success"><i class="fa fa-eye"></i> View</a></td>
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

@endsection

