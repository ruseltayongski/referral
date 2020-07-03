@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3>Facility List</h3>
            </div>
            <div class="box-body">
                @if(count($inventory) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <?php $count=0; ?>
                            <tr class="bg-black">
                                <th></th>
                                <th>Description</th>
                                <th>Capacity</th>
                                <th>Occupied</th>
                                <th>Available</th>
                            </tr>
                            <?php
                            $count = 0;
                            $facility = [];
                            ?>
                            @foreach($inventory as $row)
                                @if(!isset($facility[$row->facility]))
                                    <?php
                                        $facility[$row->facility] = true;
                                        $count++;
                                    ?>
                                    <tr>
                                        <td width="3%">{{ $count }}</td>
                                        <td colspan="5"><strong class="text-green">{{ $row->facility }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td></td>
                                    <td>{{ $row->name }}</td>
                                    <td><strong class="text-blue">
                                            {{ $row->capacity }}
                                        </strong></td>
                                    <td><strong class="text-blue">
                                            @if($row->name == 'Patients Waiting for Admission')
                                                N/A
                                            @else{{ $row->occupied }}
                                        @endif</strong></td>
                                    <td><strong class="text-green">
                                            @if($row->name == 'Patients Waiting for Admission')
                                                N/A
                                            @else
                                            {{ $row->capacity - $row->occupied }}</strong>
                                            @endif
                                    </td>
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

