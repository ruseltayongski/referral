@extends('layouts.app')

@section('content')

    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <form action="{{ asset('vaccine/vaccineview') }}" method="GET">
                    {{ csrf_field() }}
                    <div class="input-group input-group-md" style="width: 70%">
                        <input type="text" class="form-control" style="width: 100%" placeholder="Search ownership" name="search" value="{{ $search }}">
                        <span class="input-group-btn">
                            <input type="text" class="form-control" style="width: 50%" id="date_range" placeholder="Enter date range.." name="date_range" value="{{ date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)) }}">
                            <button type="submit" class="btn btn-success"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ asset('export/client/call') }}" type="button" class="btn btn-danger"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                            <button type="button" class="btn btn-warning" onclick="refreshPage()"><i class="fa fa-eye"></i> View All</button>
                            <button type="button" class="btn btn-primary" onclick="newVaccinated()"><i class="fa fa-eyedropper"></i> New Vaccinated</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="row" style="padding-left: 1%;padding-right: 1%">
                <div class="col-lg-3">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>12312321</h3>

                            <p>Sinovac</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-medkit"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3>12321</h3>

                            <p>Astrazeneca</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-medkit"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>123123</h3>

                            <p>Health Worker</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>123213</h3>

                            <p>Senior Citizen</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-android-walk"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="vaccinated_content">
                    @if(count($client)>0)
                        <table class="table table-striped table-responsive">
                            <thead class="bg-gray">
                            <tr>
                                <th>Client Name</th>
                                <th>Transacted By</th>
                                <th>Time Started</th>
                                <th>Time Ended</th>
                                <th>Time Duration</th>
                                <th>Option</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($client as $row)
                                <tr>
                                    <td>
                                        <a
                                                href="#client_modal"
                                                data-toggle="modal"
                                                data-id = "{{ $row->id }}"
                                                onclick="ClientBody('<?php echo $row->id ?>',$(this))"
                                                class="client_info"
                                        >
                                            <span class="text-green font_size">{{ $row->name }}</span><br>
                                            @if($row->transaction_complete)
                                                <small class="text-blue">Completed Call</small>
                                            @else
                                                <small class="text-red">In-Complete Call</small>
                                            @endif<br>
                                            <small class="text-yellow">{{ $row->reference_number }}</small>
                                            <?php
                                            $client_addendum = \App\ClientAddendum::where("client_id",$row->id)->count();
                                            ?>
                                            @if($client_addendum > 0)
                                                <span class="badge bg-yellow">Addendum {{ $client_addendum }}</span>
                                            @endif
                                            <br>
                                        </a>
                                    </td>
                                    <td>
                                        <?php
                                        $transacted_by = \App\User::find($row->encoded_by);
                                        $transacted_by = $transacted_by->fname.' '.ucfirst($transacted_by->mname[0]).'. '.$transacted_by->lname;
                                        ?>
                                        <span class="text-green font_size">{{ $transacted_by }}</span><br>
                                        @if($row->call_classification == 'new_call')<small class="text-blue">(New Call)</small>@else<small class="text-red">(Repeat Call)</small>@endif
                                    </td>
                                    <td>
                                            <span class="text-green font_size">
                                                {{ date('F d,Y',strtotime($row->time_started)) }}
                                            </span><br>
                                        <small class="text-yellow">
                                            ({{ date('h:i:s A',strtotime($row->time_started)) }})
                                        </small>
                                    </td>
                                    <td>
                                            <span class="text-green font_size">
                                                {{ date('F d,Y',strtotime($row->time_ended)) }}
                                            </span><br>
                                        <small class="text-yellow">
                                            ({{ date('h:i:s A',strtotime($row->time_ended)) }})
                                        </small>
                                    </td>
                                    <td>
                                            <span class="font_size text-blue">
                                                {{ date('H:i:s',strtotime($row->time_duration)) }}
                                            </span><br>
                                        <small class="text-yellow">
                                            ({{ ucfirst($row->reason_calling) }})
                                        </small>
                                    </td>
                                    <td width="10%"><button type="button" class="btn btn-sm btn-info" onclick="repeatCall('<?php echo $row->id; ?>','repeat_call')"><i class="fa fa-phone-square"></i> Repeat Call</button></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="pagination">
                            {{ $client->links() }}
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
    </div>

    <div class="modal fade" role="dialog" id="loading_modal">
        <center style="margin-top: 10%;">
            <img src="{{ asset('resources/img/loading.gif') }}" alt="">
        </center>
    </div><!-- /.modal -->

    <div class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" id="client_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body client_modal_body">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@section('js')
    <script>

        function newVaccinated(){
            $(".vaccinated_content").html(loading);
            var url = "<?php echo asset('vaccine/vaccinated_content'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".vaccinated_content").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }
    </script>

@endsection

